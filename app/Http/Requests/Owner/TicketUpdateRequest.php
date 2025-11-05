<?php

namespace App\Http\Requests\Owner;

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
            'comment' => __('catatan'),
        ];
    }
}
