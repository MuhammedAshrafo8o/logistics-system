<?php

namespace App\Modules\LocationPricing\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListAreasRequest extends FormRequest
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
                'nullable',
                Rule::exists('governorates', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
        ];
    }
}
