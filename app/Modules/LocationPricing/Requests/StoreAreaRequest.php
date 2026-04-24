<?php

namespace App\Modules\LocationPricing\Requests;

use App\Modules\LocationPricing\Models\Area;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAreaRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $exists = Area::query()
                ->where('governorate_id', $this->input('governorate_id'))
                ->where('name', $this->input('name'))
                ->exists();

            if ($exists) {
                $validator->errors()->add('name', 'The name has already been taken for this governorate.');
            }
        });
    }
}
