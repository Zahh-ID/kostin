<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\RoomType
 *
 * @OA\Schema(
 *     schema="RoomTypeResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="property_id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="area_m2", type="integer", nullable=true),
 *     @OA\Property(property="bathroom_type", type="string", nullable=true),
 *     @OA\Property(property="base_price", type="integer"),
 *     @OA\Property(property="deposit", type="integer"),
 *     @OA\Property(property="facilities_json", type="object", nullable=true),
 *     @OA\Property(property="rooms", type="array", @OA\Items(ref="#/components/schemas/RoomResource")),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 */
class RoomTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'name' => $this->name,
            'area_m2' => $this->area_m2,
            'bathroom_type' => $this->bathroom_type,
            'base_price' => $this->base_price,
            'deposit' => $this->deposit,
            'facilities_json' => $this->facilities_json,
            'rooms' => RoomResource::collection($this->whenLoaded('rooms')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
