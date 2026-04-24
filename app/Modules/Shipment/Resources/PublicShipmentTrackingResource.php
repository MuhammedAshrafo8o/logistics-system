<?php

namespace App\Modules\Shipment\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicShipmentTrackingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'shipment_number' => $this->shipment_number,
            'status' => $this->status,
            'customer_name' => $this->customer_name,
            'delivery_governorate_name' => $this->whenLoaded('deliveryGovernorate', fn () => $this->deliveryGovernorate?->name),
            'delivery_area_name' => $this->whenLoaded('deliveryArea', fn () => $this->deliveryArea?->name),
            'delivery_address' => $this->delivery_address,
            'histories' => $this->whenLoaded('histories', function () {
                return $this->histories->map(fn ($history) => [
                    'status' => $history->status,
                    'notes' => $history->notes,
                    'created_at' => $history->created_at,
                ])->values();
            }),
        ];
    }
}
