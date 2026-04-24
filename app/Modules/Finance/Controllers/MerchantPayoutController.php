<?php

namespace App\Modules\Finance\Controllers;

use App\Models\Merchant;
use App\Modules\Finance\Enums\PayoutStatus;
use App\Modules\Finance\Models\MerchantPayout;
use App\Modules\Finance\Requests\ListMerchantPayoutsRequest;
use App\Modules\Finance\Requests\StoreMerchantPayoutRequest;
use App\Modules\Finance\Requests\UpdateMerchantPayoutRequest;
use App\Modules\Finance\Resources\MerchantPayoutResource;
use App\Modules\Finance\Services\FinanceSummaryService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MerchantPayoutController
{
    public function __construct(
        private readonly FinanceSummaryService $financeSummaryService,
    ) {
    }

    public function index(ListMerchantPayoutsRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $payouts = MerchantPayout::query()
            ->with(['merchant', 'createdBy'])
            ->when(isset($validated['merchant_id']), fn ($query) => $query->where('merchant_id', $validated['merchant_id']))
            ->when(isset($validated['status']), fn ($query) => $query->where('status', $validated['status']))
            ->when(isset($validated['payment_method']), fn ($query) => $query->where('payment_method', $validated['payment_method']))
            ->when(isset($validated['date_from']), fn ($query) => $query->whereDate('created_at', '>=', $validated['date_from']))
            ->when(isset($validated['date_to']), fn ($query) => $query->whereDate('created_at', '<=', $validated['date_to']))
            ->latest()
            ->get();

        return MerchantPayoutResource::collection($payouts);
    }

    public function store(StoreMerchantPayoutRequest $request): JsonResponse
    {
        $data = $this->preparePayload($request->validated());
        $merchant = Merchant::query()->findOrFail($data['merchant_id']);
        $this->ensurePayoutWithinAvailableBalance($merchant, $data);
        $data['created_by'] = $request->user()?->id;

        $payout = MerchantPayout::create($data);

        return (new MerchantPayoutResource($payout->load(['merchant', 'createdBy'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(MerchantPayout $merchantPayout): MerchantPayoutResource
    {
        return new MerchantPayoutResource($merchantPayout->load(['merchant', 'createdBy']));
    }

    public function update(UpdateMerchantPayoutRequest $request, MerchantPayout $merchantPayout): MerchantPayoutResource
    {
        $data = $this->preparePayload($request->validated(), $merchantPayout);
        $this->ensurePayoutWithinAvailableBalance($merchantPayout->merchant, $data, $merchantPayout);

        $merchantPayout->update($data);

        return new MerchantPayoutResource($merchantPayout->fresh()->load(['merchant', 'createdBy']));
    }

    public function destroy(MerchantPayout $merchantPayout): JsonResponse
    {
        $merchantPayout->delete();

        return response()->json([
            'message' => 'Merchant payout deleted successfully',
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function preparePayload(array $data, ?MerchantPayout $merchantPayout = null): array
    {
        $status = $data['status'] ?? $merchantPayout?->status ?? PayoutStatus::PENDING;
        $hasPaidAt = array_key_exists('paid_at', $data);

        if ($status === PayoutStatus::COMPLETED && ! $hasPaidAt) {
            $data['paid_at'] = now();
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function ensurePayoutWithinAvailableBalance(Merchant $merchant, array $data, ?MerchantPayout $merchantPayout = null): void
    {
        $status = $data['status'] ?? $merchantPayout?->status ?? PayoutStatus::PENDING;

        if ($status === PayoutStatus::CANCELLED) {
            return;
        }

        $amount = (float) ($data['amount'] ?? $merchantPayout?->amount ?? 0);
        $availableBalance = $this->financeSummaryService->getMerchantAvailableBalance($merchant, $merchantPayout);

        if ($amount > $availableBalance) {
            throw new HttpResponseException(response()->json([
                'message' => 'Payout amount exceeds merchant available balance.',
            ], 422));
        }
    }
}
