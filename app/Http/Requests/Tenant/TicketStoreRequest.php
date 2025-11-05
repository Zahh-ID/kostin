<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'category' => ['required', Rule::in(['technical', 'payment', 'content', 'abuse'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'subject' => __('subjek'),
            'description' => __('deskripsi'),
            'category' => __('kategori'),
            'priority' => __('prioritas'),
        ];
    }
}
