<?php

namespace App\Modules\Merchant\Controllers;

use App\Models\Merchant;
use App\Modules\Merchant\Requests\StoreMerchantRequest;
use App\Modules\Merchant\Requests\UpdateMerchantRequest;
use App\Modules\Merchant\Resources\MerchantResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MerchantController
{
    public function index(): AnonymousResourceCollection
    {
        $merchants = Merchant::query()->latest()->get();

        return MerchantResource::collection($merchants);
    }

    public function store(StoreMerchantRequest $request): JsonResponse
    {
        $merchant = Merchant::create([
            'name' => $request->string('name')->toString(),
            'company_name' => $request->input('company_name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'status' => $request->input('status', 'active'),
        ]);

        return (new MerchantResource($merchant))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Merchant $merchant): MerchantResource
    {
        return new MerchantResource($merchant);
    }

    public function update(UpdateMerchantRequest $request, Merchant $merchant): MerchantResource
    {
        $merchant->update($request->validated());

        return new MerchantResource($merchant->fresh());
    }

    public function destroy(Merchant $merchant): JsonResponse
    {
        $merchant->delete();

        return response()->json([
            'message' => 'Merchant deleted successfully',
        ]);
    }
}
