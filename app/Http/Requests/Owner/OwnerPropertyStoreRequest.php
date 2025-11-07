<?php

namespace App\Http\Requests\Owner;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class OwnerPropertyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_OWNER;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'rules_text' => ['nullable', 'string'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['string'],
        ];
    }
}
