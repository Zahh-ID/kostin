<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Property
 *
 * @OA\Schema(
 *     schema="PropertyResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="owner", ref="#/components/schemas/UserResource"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="lat", type="number", format="float", nullable=true),
 *     @OA\Property(property="lng", type="number", format="float", nullable=true),
 *     @OA\Property(property="rules_text", type="string", nullable=true),
 *     @OA\Property(property="photos", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="status", type="string", enum={"draft","pending","approved","rejected"}),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="room_types", type="array", @OA\Items(ref="#/components/schemas/RoomTypeResource"))
 * )
 */
class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner' => new UserResource($this->whenLoaded('owner')),
            'name' => $this->name,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'rules_text' => $this->rules_text,
            'photos' => $this->photos,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'room_types' => RoomTypeResource::collection($this->whenLoaded('roomTypes')),
        ];
    }
}
