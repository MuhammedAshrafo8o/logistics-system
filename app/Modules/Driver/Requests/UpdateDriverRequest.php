<?php

namespace App\Modules\Driver\Requests;

use App\Modules\Driver\Enums\DriverStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends FormRequest
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
            'user_id' => ['sometimes', 'nullable', Rule::exists('users', 'id')->where(fn ($query) => $query->whereNull('deleted_at'))],
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:50'],
            'national_id' => ['sometimes', 'nullable', 'string', 'max:100'],
            'vehicle_type' => ['sometimes', 'nullable', 'string', 'max:100'],
            'vehicle_plate' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'nullable', Rule::in(DriverStatus::values())],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
