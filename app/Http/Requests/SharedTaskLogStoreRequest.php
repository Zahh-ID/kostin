<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SharedTaskLogStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shared_task_id' => ['required', 'exists:shared_tasks,id'],
            'run_at' => ['required', 'date'],
            'photo_url' => ['nullable', 'string', 'max:2048'],
            'note' => ['nullable', 'string'],
        ];
    }
}
