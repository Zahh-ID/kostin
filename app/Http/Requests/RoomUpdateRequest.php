<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_code' => ['sometimes', 'string', 'max:100'],
            'custom_price' => ['nullable', 'integer', 'min:0'],
            'status' => ['sometimes', 'in:available,occupied,maintenance'],
            'facilities_override_json' => ['nullable', 'array'],
        ];
    }
}
