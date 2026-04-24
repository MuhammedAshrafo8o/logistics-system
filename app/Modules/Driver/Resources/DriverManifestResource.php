<?php

namespace App\Modules\Driver\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverManifestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $shipments = collect($this->resource['shipments']);

        return [
            'driver' => [
                'id' => $this->resource['driver']->id,
                'name' => $this->resource['driver']->name,
                'phone' => $this->resource['driver']->phone,
                'vehicle_type' => $this->resource['driver']->vehicle_type,
                'vehicle_plate' => $this->resource['driver']->vehicle_plate,
            ],
            'generated_at' => $this->resource['generated_at'],
            'summary' => [
                'total_shipments' => $shipments->count(),
                'total_cod_shipments' => $shipments->where('order.payment_type', 'cod')->count(),
                'total_prepaid_shipments' => $shipments->where('order.payment_type', 'prepaid')->count(),
                'total_cod_amount' => number_format((float) $shipments->sum(fn ($shipment) => (float) $shipment->cod_amount), 2, '.', ''),
                'total_shipping_fee' => number_format((float) $shipments->sum(fn ($shipment) => (float) $shipment->shipping_fee), 2, '.', ''),
            ],
            'shipments' => $shipments->map(function ($shipment): array {
                return [
                    'shipment_id' => $shipment->id,
                    'shipment_number' => $shipment->shipment_number,
                    'status' => $shipment->status,
                    'customer_name' => $shipment->customer_name,
                    'customer_phone' => $shipment->customer_phone,
                    'delivery_governorate_name' => $shipment->deliveryGovernorate?->name,
                    'delivery_area_name' => $shipment->deliveryArea?->name,
                    'delivery_address' => $shipment->delivery_address,
                    'cod_amount' => $shipment->cod_amount,
                    'shipping_fee' => $shipment->shipping_fee,
                    'merchant_name' => $shipment->merchant?->name,
                    'merchant_phone' => $shipment->merchant?->phone ?? null,
                    'notes' => $shipment->tracking_notes,
                ];
            })->values(),
        ];
    }
}
