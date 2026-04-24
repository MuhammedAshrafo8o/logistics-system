<?php

namespace App\Modules\LocationPricing\Controllers;

use App\Modules\LocationPricing\Models\ShippingRate;
use App\Modules\LocationPricing\Requests\CalculateShippingRateRequest;
use App\Modules\LocationPricing\Requests\StoreShippingRateRequest;
use App\Modules\LocationPricing\Requests\UpdateShippingRateRequest;
use App\Modules\LocationPricing\Resources\ShippingRateResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShippingRateController
{
    public function index(): AnonymousResourceCollection
    {
        $shippingRates = ShippingRate::query()
            ->with(['governorate', 'area'])
            ->latest()
            ->get();

        return ShippingRateResource::collection($shippingRates);
    }

    public function store(StoreShippingRateRequest $request): JsonResponse
    {
        $shippingRate = ShippingRate::create([
            'governorate_id' => $request->integer('governorate_id'),
            'area_id' => $request->input('area_id'),
            'shipping_fee' => $request->input('shipping_fee'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return (new ShippingRateResource($shippingRate->load(['governorate', 'area'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(ShippingRate $shippingRate): ShippingRateResource
    {
        return new ShippingRateResource($shippingRate->load(['governorate', 'area']));
    }

    public function update(UpdateShippingRateRequest $request, ShippingRate $shippingRate): ShippingRateResource
    {
        $validated = $request->validated();

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        $shippingRate->update($validated);

        return new ShippingRateResource($shippingRate->fresh()->load(['governorate', 'area']));
    }

    public function destroy(ShippingRate $shippingRate): JsonResponse
    {
        $shippingRate->delete();

        return response()->json([
            'message' => 'Shipping rate deleted successfully',
        ]);
    }

    public function calculate(CalculateShippingRateRequest $request): JsonResponse
    {
        $governorateId = $request->integer('governorate_id');
        $areaId = $request->input('area_id');

        if ($areaId !== null) {
            $areaRate = ShippingRate::query()
                ->where('governorate_id', $governorateId)
                ->where('area_id', $areaId)
                ->where('is_active', true)
                ->first();

            if ($areaRate !== null) {
                return response()->json([
                    'data' => [
                        'governorate_id' => $areaRate->governorate_id,
                        'area_id' => $areaRate->area_id,
                        'shipping_fee' => number_format((float) $areaRate->shipping_fee, 2, '.', ''),
                        'source' => 'area',
                    ],
                ]);
            }
        }

        $governorateRate = ShippingRate::query()
            ->where('governorate_id', $governorateId)
            ->whereNull('area_id')
            ->where('is_active', true)
            ->first();

        if ($governorateRate === null) {
            return response()->json([
                'message' => 'No active shipping rate found for this location.',
            ], 404);
        }

        return response()->json([
            'data' => [
                'governorate_id' => $governorateRate->governorate_id,
                'area_id' => $areaId,
                'shipping_fee' => number_format((float) $governorateRate->shipping_fee, 2, '.', ''),
                'source' => 'governorate',
            ],
        ]);
    }
}
