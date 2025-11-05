<?php

namespace App\Http\Requests\Admin;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                Ticket::STATUS_OPEN,
                Ticket::STATUS_IN_REVIEW,
                Ticket::STATUS_ESCALATED,
                Ticket::STATUS_RESOLVED,
                Ticket::STATUS_REJECTED,
            ])],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'status' => __('status'),
            'assignee_id' => __('petugas'),
            'comment' => __('catatan'),
        ];
    }
}
