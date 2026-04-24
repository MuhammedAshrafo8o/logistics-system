<?php

namespace App\Modules\Warehouse\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'governorate_id' => $this->governorate_id,
            'governorate_name' => $this->whenLoaded('governorate', fn () => $this->governorate?->name),
            'area_id' => $this->area_id,
            'area_name' => $this->whenLoaded('area', fn () => $this->area?->name),
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}
