<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'due_date' => ['nullable', 'date'],
            'late_fee' => ['nullable', 'integer', 'min:0'],
            'total' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'in:unpaid,paid,overdue,canceled'],
            'external_order_id' => ['nullable', 'string', 'max:255'],
            'qris_payload' => ['nullable', 'array'],
        ];
    }
}
