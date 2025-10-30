<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_type_id' => ['required', 'exists:room_types,id'],
            'room_code' => ['required', 'string', 'max:100'],
            'custom_price' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'in:available,occupied,maintenance'],
            'facilities_override_json' => ['nullable', 'array'],
        ];
    }
}
