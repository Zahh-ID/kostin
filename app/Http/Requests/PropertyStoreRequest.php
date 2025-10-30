<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'rules_text' => ['nullable', 'string'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['string', 'max:2048'],
            'status' => ['nullable', 'in:draft,pending,approved,rejected'],
        ];
    }
}
