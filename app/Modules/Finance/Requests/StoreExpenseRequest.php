<?php

namespace App\Modules\Finance\Requests;

use App\Modules\Finance\Enums\ExpenseCategory;
use App\Modules\Finance\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
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
            'category' => ['required', Rule::in(ExpenseCategory::values())],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['nullable', 'date'],
            'payment_method' => ['nullable', Rule::in(PaymentMethod::values())],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
