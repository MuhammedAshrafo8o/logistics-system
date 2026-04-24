<?php

namespace App\Modules\LocationPricing\Requests;

use App\Modules\LocationPricing\Models\Area;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAreaRequest extends FormRequest
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
                'sometimes',
                Rule::exists('governorates', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $area = $this->route('area');
            $governorateId = $this->input('governorate_id', $area->governorate_id);
            $name = $this->input('name', $area->name);

            $exists = Area::query()
                ->where('governorate_id', $governorateId)
                ->where('name', $name)
                ->whereKeyNot($area->id)
                ->exists();

            if ($exists) {
                $validator->errors()->add('name', 'The name has already been taken for this governorate.');
            }
        });
    }
}
