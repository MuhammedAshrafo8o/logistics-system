<?php

namespace App\Modules\Order\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'merchant_id' => $this->merchant_id,
            'merchant_name' => $this->whenLoaded('merchant', fn () => $this->merchant?->name),
            'order_number' => $this->order_number,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_phone_alt' => $this->customer_phone_alt,
            'delivery_governorate_id' => $this->delivery_governorate_id,
            'delivery_governorate_name' => $this->whenLoaded('deliveryGovernorate', fn () => $this->deliveryGovernorate?->name),
            'delivery_area_id' => $this->delivery_area_id,
            'delivery_area_name' => $this->whenLoaded('deliveryArea', fn () => $this->deliveryArea?->name),
            'delivery_address' => $this->delivery_address,
            'delivery_notes' => $this->delivery_notes,
            'pickup_governorate_id' => $this->pickup_governorate_id,
            'pickup_area_id' => $this->pickup_area_id,
            'pickup_address' => $this->pickup_address,
            'pickup_notes' => $this->pickup_notes,
            'cod_amount' => $this->cod_amount,
            'shipping_fee' => $this->shipping_fee,
            'payment_type' => $this->payment_type,
            'fulfillment_type' => $this->fulfillment_type,
            'is_fragile' =>(bool) $this->is_fragile,
            'allow_inspection' =>(bool) $this->allow_inspection,
            'requires_packaging' =>(bool) $this->requires_packaging,
            'package_notes' => $this->package_notes,
            'source' => $this->source,
            'external_source' => $this->external_source,
            'external_order_id' => $this->external_order_id,
            'external_order_number' => $this->external_order_number,
            'requires_review' =>(bool) $this->requires_review,
            'review_reason' => $this->review_reason,
            'status' => $this->status,
            'notes' => $this->notes,
            'items' => $this->whenLoaded('items', fn () => OrderItemResource::collection($this->items)),
            'stock_reservations' => $this->whenLoaded('stockReservations', fn () => \App\Modules\Warehouse\Resources\OrderStockReservationResource::collection($this->stockReservations)),
            'created_at' => $this->created_at,
        ];
    }
}
