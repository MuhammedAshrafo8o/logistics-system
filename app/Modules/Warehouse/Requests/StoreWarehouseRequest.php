<?php

namespace App\Modules\Warehouse\Requests;

use App\Modules\LocationPricing\Models\Area;
use App\Modules\Warehouse\Enums\WarehouseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWarehouseRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100', Rule::unique('warehouses', 'code')],
            'address' => ['nullable', 'string'],
            'governorate_id' => [
                'nullable',
                Rule::exists('governorates', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'area_id' => [
                'nullable',
                Rule::exists('areas', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'status' => ['nullable', Rule::in(WarehouseStatus::values())],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $areaId = $this->input('area_id');

            if ($validator->errors()->isNotEmpty() || $areaId === null || $this->input('governorate_id') === null) {
                return;
            }

            $belongs = Area::query()
                ->whereKey($areaId)
                ->where('governorate_id', $this->input('governorate_id'))
                ->exists();

            if (! $belongs) {
                $validator->errors()->add('area_id', 'The selected area does not belong to the selected governorate.');
            }
        });
    }
}
