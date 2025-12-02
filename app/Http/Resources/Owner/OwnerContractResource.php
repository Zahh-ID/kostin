<?php

namespace App\Http\Resources\Owner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Contract
 */
class OwnerContractResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'price' => (int) $this->price_per_month,
            'billing_day' => $this->billing_day,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'property_name' => $this->room?->roomType?->property?->name,
            'room_name' => $this->room?->room_code,
            'tenant_name' => $this->tenant?->name,
            'terminated_at' => $this->terminated_at,
            'termination_reason' => $this->termination_reason,
        ];
    }
}
