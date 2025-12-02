<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\RoomType
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
