<?php

namespace App\Modules\Finance\Requests;

use App\Modules\Finance\Enums\DriverCashClosureStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateDriverCashClosureRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'received_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(DriverCashClosureStatus::values())],
            'notes' => ['nullable', 'string'],
        ];
    }
}
