<?php

namespace App\Http\Requests\Owner;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class OwnerPropertyUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Property|null $property */
        $property = $this->route('property');
        $user = $this->user();

        return $user?->role === User::ROLE_OWNER && $property?->owner_id === $user->id;
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
