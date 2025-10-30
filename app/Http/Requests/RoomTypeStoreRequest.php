<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomTypeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'name' => ['required', 'string', 'max:255'],
            'area_m2' => ['nullable', 'integer', 'min:0'],
            'bathroom_type' => ['nullable', 'in:inside,outside'],
            'base_price' => ['required', 'integer', 'min:0'],
            'deposit' => ['nullable', 'integer', 'min:0'],
            'facilities_json' => ['nullable', 'array'],
        ];
    }
}
