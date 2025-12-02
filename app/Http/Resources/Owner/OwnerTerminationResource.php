<?php

namespace App\Http\Resources\Owner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ContractTerminationRequest
 */
class OwnerTerminationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'reason' => $this->reason,
            'requested_end_date' => $this->requested_end_date,
            'contract' => [
                'id' => $this->contract?->id,
                'room' => $this->contract?->room?->room_code,
                'property' => $this->contract?->room?->roomType?->property?->name,
            ],
            'tenant' => [
                'id' => $this->tenant?->id,
                'name' => $this->tenant?->name,
            ],
        ];
    }
}
