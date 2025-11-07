<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'owner';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['available', 'occupied', 'maintenance'])],
            'custom_price' => ['required', 'integer', 'min:0'],
            'description' => ['required', 'string'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'max:5120'],
            'remove_photos' => ['nullable', 'array'],
            'remove_photos.*' => ['string'],
            'facilities_override' => ['nullable', 'array'],
            'facilities_override.*' => ['nullable', 'string', 'max:100'],
        ];
    }
}
