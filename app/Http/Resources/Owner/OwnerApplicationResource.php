<?php

namespace App\Http\Resources\Owner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\RentalApplication
 */
class OwnerApplicationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'tenant_name' => $this->tenant?->name,
            'property_name' => $this->property?->name,
            'room_name' => $this->room?->room_code,
            'tenant_notes' => $this->tenant_notes,
        ];
    }
}
