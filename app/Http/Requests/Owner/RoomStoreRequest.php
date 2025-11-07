<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomStoreRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->filled('room_type_id') && $this->route('room_type')) {
            $this->merge([
                'room_type_id' => $this->route('room_type')->getKey(),
            ]);
        }
    }

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
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'room_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rooms', 'room_code')
                    ->where(fn ($query) => $query->where('room_type_id', $this->input('room_type_id'))),
            ],
            'status' => ['required', Rule::in(['available', 'occupied', 'maintenance'])],
            'custom_price' => ['required', 'integer', 'min:0'],
            'description' => ['required', 'string'],
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['image', 'max:5120'],
            'facilities_override' => ['nullable', 'array'],
            'facilities_override.*' => ['nullable', 'string', 'max:100'],
        ];
    }
}
