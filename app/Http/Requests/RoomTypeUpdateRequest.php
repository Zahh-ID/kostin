<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomTypeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'area_m2' => ['nullable', 'integer', 'min:0'],
            'bathroom_type' => ['nullable', 'in:inside,outside'],
            'base_price' => ['sometimes', 'integer', 'min:0'],
            'deposit' => ['nullable', 'integer', 'min:0'],
            'facilities_json' => ['nullable', 'array'],
        ];
    }
}
