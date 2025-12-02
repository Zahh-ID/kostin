<?php

namespace App\Http\Requests\Owner;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class OwnerPropertyPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Property|null $property */
        $property = $this->route('property');
        $user = $this->user();

        return $user?->role === User::ROLE_OWNER && $property?->owner_id === $user->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'max:5120'],
        ];
    }
}
