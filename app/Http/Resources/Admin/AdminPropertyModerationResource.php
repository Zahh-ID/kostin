<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Property
 */
class AdminPropertyModerationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'status' => $this->status,
            'moderation_notes' => $this->moderation_notes,
            'owner' => [
                'id' => $this->owner?->id,
                'name' => $this->owner?->name,
                'email' => $this->owner?->email,
            ],
        ];
    }
}
