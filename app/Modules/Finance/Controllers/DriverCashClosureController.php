<?php

namespace App\Modules\Finance\Controllers;

use App\Modules\Driver\Models\Driver;
use App\Modules\Finance\Enums\DriverCashClosureStatus;
use App\Modules\Finance\Models\DriverCashClosure;
use App\Modules\Finance\Requests\ListDriverCashClosuresRequest;
use App\Modules\Finance\Requests\StoreDriverCashClosureRequest;
use App\Modules\Finance\Requests\UpdateDriverCashClosureRequest;
use App\Modules\Finance\Resources\DriverCashClosureResource;
use App\Modules\Finance\Resources\DriverExpectedCashResource;
use App\Modules\Finance\Services\FinanceSummaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DriverCashClosureController
{
    public function __construct(
        private readonly FinanceSummaryService $financeSummaryService,
    ) {
    }

    public function index(ListDriverCashClosuresRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $closures = DriverCashClosure::query()
            ->with(['driver', 'createdBy', 'verifiedBy'])
            ->when(isset($validated['driver_id']), fn ($query) => $query->where('driver_id', $validated['driver_id']))
            ->when(isset($validated['status']), fn ($query) => $query->where('status', $validated['status']))
            ->when(isset($validated['date_from']), fn ($query) => $query->whereDate('created_at', '>=', $validated['date_from']))
            ->when(isset($validated['date_to']), fn ($query) => $query->whereDate('created_at', '<=', $validated['date_to']))
            ->latest()
            ->get();

        return DriverCashClosureResource::collection($closures);
    }

    public function store(StoreDriverCashClosureRequest $request): JsonResponse
    {
        $closure = DriverCashClosure::create(
            $this->preparePayload($request->validated(), null, $request->user()?->id)
        );

        return (new DriverCashClosureResource($closure->load(['driver', 'createdBy', 'verifiedBy'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(DriverCashClosure $driverCashClosure): DriverCashClosureResource
    {
        return new DriverCashClosureResource($driverCashClosure->load(['driver', 'createdBy', 'verifiedBy']));
    }

    public function update(UpdateDriverCashClosureRequest $request, DriverCashClosure $driverCashClosure): DriverCashClosureResource
    {
        $driverCashClosure->update(
            $this->preparePayload($request->validated(), $driverCashClosure, $request->user()?->id)
        );

        return new DriverCashClosureResource($driverCashClosure->fresh()->load(['driver', 'createdBy', 'verifiedBy']));
    }

    public function destroy(DriverCashClosure $driverCashClosure): JsonResponse
    {
        $driverCashClosure->delete();

        return response()->json([
            'message' => 'Driver cash closure deleted successfully',
        ]);
    }

    public function expected(Driver $driver): DriverExpectedCashResource
    {
        return new DriverExpectedCashResource([
            'driver_id' => $driver->id,
            'expected_amount' => number_format(
                $this->financeSummaryService->getDriverExpectedCash($driver),
                2,
                '.',
                ''
            ),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function preparePayload(array $data, ?DriverCashClosure $driverCashClosure, ?int $userId): array
    {
        $expectedAmount = (float) ($data['expected_amount'] ?? $driverCashClosure?->expected_amount ?? 0);
        $receivedAmount = (float) ($data['received_amount'] ?? $driverCashClosure?->received_amount ?? 0);
        $status = $data['status'] ?? $driverCashClosure?->status ?? DriverCashClosureStatus::PENDING;

        $data['difference_amount'] = number_format($receivedAmount - $expectedAmount, 2, '.', '');

        if ($driverCashClosure === null) {
            $data['created_by'] = $userId;
        }

        if (
            $status === DriverCashClosureStatus::VERIFIED
            && ($driverCashClosure?->verified_at === null)
            && ! array_key_exists('verified_at', $data)
        ) {
            $data['verified_by'] = $userId;
            $data['verified_at'] = now();
        }

        return $data;
    }
}
