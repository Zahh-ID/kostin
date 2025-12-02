<?php

namespace App\Http\Resources\Owner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\OwnerWallet
 */
class OwnerWalletResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'balance' => (int) $this->balance,
        ];
    }
}
