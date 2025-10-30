<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Payment
 *
 * @OA\Schema(
 *     schema="PaymentResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="invoice_id", type="integer"),
 *     @OA\Property(property="midtrans_order_id", type="string", nullable=true),
 *     @OA\Property(property="payment_type", type="string", enum={"qris"}),
 *     @OA\Property(property="amount", type="integer"),
 *     @OA\Property(property="status", type="string", enum={"success","pending","failed"}),
 *     @OA\Property(property="paid_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="raw_webhook_json", type="object", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 */
class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'midtrans_order_id' => $this->midtrans_order_id,
            'payment_type' => $this->payment_type,
            'amount' => $this->amount,
            'status' => $this->status,
            'paid_at' => $this->paid_at?->toIso8601String(),
            'raw_webhook_json' => $this->raw_webhook_json,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
