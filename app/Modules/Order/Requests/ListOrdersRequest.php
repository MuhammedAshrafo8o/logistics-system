<?php

namespace App\Modules\Order\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListOrdersRequest extends FormRequest
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
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'merchant_id' => [
                'nullable',
                Rule::exists('merchants', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'merchant_name' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
            'fulfillment_type' => ['nullable', 'string'],
            'payment_type' => ['nullable', 'string'],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
