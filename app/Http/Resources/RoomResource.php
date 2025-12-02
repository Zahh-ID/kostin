<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Room
 *
 * @OA\Schema(
 *     schema="RoomResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="room_type_id", type="integer"),
 *     @OA\Property(property="room_code", type="string"),
 *     @OA\Property(property="custom_price", type="integer", nullable=true),
 *     @OA\Property(property="status", type="string", enum={"available","occupied","maintenance"}),
 *     @OA\Property(property="facilities_override_json", type="object", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 */
class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_type_id' => $this->room_type_id,
            'room_code' => $this->room_code,
            'custom_price' => $this->custom_price,
            'status' => $this->status,
            'facilities_override_json' => $this->facilities_override_json,
            'description' => $this->description,
            'photos' => $this->photos_json,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'room_type' => new RoomTypeResource($this->whenLoaded('roomType')),
        ];
    }
}
