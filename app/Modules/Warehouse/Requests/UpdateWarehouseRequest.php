<?php

namespace App\Modules\Warehouse\Requests;

use App\Modules\LocationPricing\Models\Area;
use App\Modules\Warehouse\Enums\WarehouseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $warehouse = $this->route('warehouse');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'max:100', Rule::unique('warehouses', 'code')->ignore($warehouse?->id)],
            'address' => ['sometimes', 'nullable', 'string'],
            'governorate_id' => [
                'sometimes',
                'nullable',
                Rule::exists('governorates', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'area_id' => [
                'sometimes',
                'nullable',
                Rule::exists('areas', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'status' => ['sometimes', Rule::in(WarehouseStatus::values())],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $warehouse = $this->route('warehouse');
            $governorateId = $this->has('governorate_id') ? $this->input('governorate_id') : $warehouse?->governorate_id;
            $areaId = $this->has('area_id') ? $this->input('area_id') : $warehouse?->area_id;

            if ($areaId === null || $governorateId === null) {
                return;
            }

            $belongs = Area::query()
                ->whereKey($areaId)
                ->where('governorate_id', $governorateId)
                ->exists();

            if (! $belongs) {
                $validator->errors()->add('area_id', 'The selected area does not belong to the selected governorate.');
            }
        });
    }
}
