<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class ManualPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'exists:payment_accounts,method'],
            'notes' => ['nullable', 'string', 'max:500'],
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'payment_method' => 'metode pembayaran',
            'notes' => 'catatan',
            'proof' => 'bukti pembayaran',
        ];
    }
}
