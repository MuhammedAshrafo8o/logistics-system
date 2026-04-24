<?php

namespace App\Modules\LocationPricing\Requests;

use App\Modules\LocationPricing\Models\Area;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CalculateShippingRateRequest extends FormRequest
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
            'governorate_id' => [
                'required',
                Rule::exists('governorates', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'area_id' => [
                'nullable',
                Rule::exists('areas', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty() || $this->input('area_id') === null) {
                return;
            }

            $belongsToGovernorate = Area::query()
                ->whereKey($this->input('area_id'))
                ->where('governorate_id', $this->input('governorate_id'))
                ->exists();

            if (! $belongsToGovernorate) {
                $validator->errors()->add('area_id', 'The selected area does not belong to the selected governorate.');
            }
        });
    }
}
