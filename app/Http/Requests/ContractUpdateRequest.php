<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'price_per_month' => ['nullable', 'integer', 'min:0'],
            'billing_day' => ['nullable', 'integer', 'between:1,28'],
            'deposit_amount' => ['nullable', 'integer', 'min:0'],
            'grace_days' => ['nullable', 'integer', 'min:0', 'max:31'],
            'late_fee_per_day' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'in:active,ended,canceled'],
        ];
    }
}
