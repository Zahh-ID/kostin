<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'rules_text' => ['nullable', 'string'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['string', 'max:2048'],
            'status' => ['sometimes', 'in:draft,pending,approved,rejected'],
        ];
    }
}
