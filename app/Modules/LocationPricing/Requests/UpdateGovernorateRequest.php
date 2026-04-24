<?php

namespace App\Modules\LocationPricing\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGovernorateRequest extends FormRequest
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
        $governorate = $this->route('governorate');
        $governorateId = $governorate?->id ?? $governorate;

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('governorates', 'name')->ignore($governorateId)],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
