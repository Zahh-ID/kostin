<?php

namespace App\Http\Resources\Owner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Payment
 */
class OwnerManualPaymentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'payment_type' => $this->manual_method ?? $this->payment_type,
            'amount' => (int) $this->amount,
            'created_at' => $this->created_at,
            'contract_info' => ($this->invoice?->contract?->room?->roomType?->property?->name ?? '') . ' · ' . ($this->invoice?->contract?->room?->room_code ?? ''),
            'tenant_name' => $this->submitter?->name,
        ];
    }
}
