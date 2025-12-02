<?php

namespace App\Http\Resources\Owner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Ticket
 */
class OwnerTicketResource extends JsonResource
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
            'category' => $this->category,
            'assignee' => [
                'id' => $this->assignee?->id,
                'name' => $this->assignee?->name,
            ],
            'reporter' => [
                'id' => $this->reporter?->id,
                'name' => $this->reporter?->name,
            ],
            'related' => [
                'type' => $this->related_type,
                'id' => $this->related_id,
                'property' => $this->related_type === \App\Models\Property::class
                    ? [
                        'id' => $this->related_id,
                        'name' => optional($this->related)->name,
                    ]
                    : null,
            ],
            'created_at' => $this->created_at,
        ];
    }
}
