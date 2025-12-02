<?php

namespace App\Http\Resources\Owner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Room
 */
class OwnerRoomResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_code' => $this->room_code,
            'status' => $this->status,
            'price' => (int) ($this->custom_price ?? $this->roomType?->base_price ?? 0),
            'room_type' => [
                'id' => $this->roomType?->id,
                'name' => $this->roomType?->name,
                'property' => $this->roomType?->property?->name,
            ],
            'facilities' => $this->facilities_override_json ?? $this->roomType?->facilities_json ?? [],
        ];
    }
}
