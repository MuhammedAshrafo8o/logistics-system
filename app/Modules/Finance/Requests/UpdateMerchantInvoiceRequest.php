<?php

namespace App\Modules\Finance\Requests;

use App\Modules\Finance\Enums\MerchantInvoiceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMerchantInvoiceRequest extends FormRequest
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
            'period_start' => ['sometimes', 'nullable', 'date'],
            'period_end' => ['sometimes', 'nullable', 'date', 'after_or_equal:period_start'],
            'status' => ['sometimes', Rule::in(MerchantInvoiceStatus::values())],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
