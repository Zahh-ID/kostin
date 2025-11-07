<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SocialRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('socialite_google_user');
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'in:tenant,owner'],
        ];
    }
}
