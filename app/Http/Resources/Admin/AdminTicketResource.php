<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Ticket
 */
class AdminTicketResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_code' => $this->ticket_code,
            'subject' => $this->subject,
            'status' => $this->status,
            'priority' => $this->priority,
            'reporter' => [
                'id' => $this->reporter?->id,
                'name' => $this->reporter?->name,
            ],
            'assignee' => [
                'id' => $this->assignee?->id,
                'name' => $this->assignee?->name,
            ],
            'created_at' => $this->created_at,
        ];
    }
}
