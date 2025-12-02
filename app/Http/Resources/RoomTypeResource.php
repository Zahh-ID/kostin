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
 *     @OA\Property(property="property", ref="#/components/schemas/PropertyResource"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="base_price", type="number", format="float"),
 *     @OA\Property(property="area_m2", type="number", format="float"),
 *     @OA\Property(property="bathroom_type", type="string", enum={"inside","outside"}),
 *     @OA\Property(property="deposit", type="number", format="float"),
 *     @OA\Property(property="facilities_json", type="object"),
 *     @OA\Property(property="rooms", type="array", @OA\Items(ref="#/components/schemas/RoomResource"))
 * )
 */
class RoomTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property' => new PropertyResource($this->whenLoaded('property')),
            'name' => $this->name,
            'base_price' => $this->base_price,
            'area_m2' => $this->area_m2,
            'bathroom_type' => $this->bathroom_type,
            'deposit' => $this->deposit,
            'facilities_json' => $this->facilities_json,
            'rooms' => RoomResource::collection($this->whenLoaded('rooms')),
        ];
    }
}
