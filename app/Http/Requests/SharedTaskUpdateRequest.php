<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SharedTaskUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'rrule' => ['nullable', 'string', 'max:255'],
            'next_run_at' => ['nullable', 'date'],
            'assignee_user_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
