<?php

namespace App\Http\Resources\Owner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Owner\OwnerRoomTypeResource;

/**
 * @mixin \App\Models\Property
 */
class OwnerPropertyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $photos = $this->photos ?? [];

        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'rules_text' => $this->rules_text,
            'photos' => $photos,
            'status' => $this->status,
            'moderation_notes' => $this->moderation_notes,
            'room_types_count' => $this->room_types_count ?? $this->roomTypes()->count(),
            'room_types' => OwnerRoomTypeResource::collection($this->whenLoaded('roomTypes')),
            'cover_photo' => $photos[0] ?? null,
        ];
    }
}
