<?php

namespace App\Modules\Order\Services;

use App\Modules\LocationPricing\Models\ShippingRate;

class OrderShippingFeeService
{
    /**
     * @return array{shipping_fee:string, source:string|null}
     */
    public function resolve(int $governorateId, ?int $areaId): array
    {
        if ($areaId !== null) {
            $areaRate = ShippingRate::query()
                ->where('governorate_id', $governorateId)
                ->where('area_id', $areaId)
                ->where('is_active', true)
                ->first();

            if ($areaRate !== null) {
                return [
                    'shipping_fee' => (string) $areaRate->shipping_fee,
                    'source' => 'area',
                ];
            }
        }

        $governorateRate = ShippingRate::query()
            ->where('governorate_id', $governorateId)
            ->whereNull('area_id')
            ->where('is_active', true)
            ->first();

        if ($governorateRate !== null) {
            return [
                'shipping_fee' => (string) $governorateRate->shipping_fee,
                'source' => 'governorate',
            ];
        }

        return [
            'shipping_fee' => '0.00',
            'source' => null,
        ];
    }
}
