<?php

namespace App\Http\Resources\Owner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\RoomType
 */
class OwnerRoomTypeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'base_price' => (int) $this->base_price,
            'deposit' => (int) ($this->deposit ?? 0),
            'rooms_count' => $this->rooms_count ?? $this->rooms()->count(),
            'property' => [
                'id' => $this->property?->id,
                'name' => $this->property?->name,
            ],
        ];
    }
}
