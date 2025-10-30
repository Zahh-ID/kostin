<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Invoice
 *
 * @OA\Schema(
 *     schema="InvoiceResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="contract_id", type="integer"),
 *     @OA\Property(property="contract", ref="#/components/schemas/ContractResource"),
 *     @OA\Property(property="period_month", type="integer"),
 *     @OA\Property(property="period_year", type="integer"),
 *     @OA\Property(property="due_date", type="string", format="date", nullable=true),
 *     @OA\Property(property="amount", type="integer"),
 *     @OA\Property(property="late_fee", type="integer"),
 *     @OA\Property(property="total", type="integer"),
 *     @OA\Property(property="status", type="string", enum={"unpaid","paid","overdue","canceled"}),
 *     @OA\Property(property="external_order_id", type="string", nullable=true),
 *     @OA\Property(property="qris_payload", type="object", nullable=true),
 *     @OA\Property(property="payments", type="array", @OA\Items(ref="#/components/schemas/PaymentResource")),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 */
class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'contract_id' => $this->contract_id,
            'contract' => new ContractResource($this->whenLoaded('contract')),
            'period_month' => $this->period_month,
            'period_year' => $this->period_year,
            'due_date' => $this->due_date?->toDateString(),
            'amount' => $this->amount,
            'late_fee' => $this->late_fee,
            'total' => $this->total,
            'status' => $this->status,
            'external_order_id' => $this->external_order_id,
            'qris_payload' => $this->qris_payload,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}
