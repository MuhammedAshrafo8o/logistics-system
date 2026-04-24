<?php

namespace App\Modules\LocationPricing\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'governorate_id' => $this->governorate_id,
            'governorate_name' => $this->whenLoaded('governorate', fn () => $this->governorate?->name),
            'name' => $this->name,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
