<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'tenant';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'months_count' => ['required', 'integer', 'min:1', 'max:12'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'months_count' => (int) $this->input('months_count', 1),
        ]);
    }
}
