<?php

namespace App\Modules\Shipment\Requests;

use App\Modules\Shipment\Enums\ShipmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShipmentStatusRequest extends FormRequest
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
            'status' => ['required', Rule::in(ShipmentStatus::values())],
            'notes' => ['nullable', 'string'],
        ];
    }
}
