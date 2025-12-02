<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Contract
 *
 * @OA\Schema(
 *     schema="ContractResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="tenant", ref="#/components/schemas/UserResource"),
 *     @OA\Property(property="room", ref="#/components/schemas/RoomResource"),
 *     @OA\Property(property="price_per_month", type="integer"),
 *     @OA\Property(property="billing_day", type="integer"),
 *     @OA\Property(property="deposit_amount", type="integer"),
 *     @OA\Property(property="grace_days", type="integer"),
 *     @OA\Property(property="late_fee_per_day", type="integer"),
 *     @OA\Property(property="status", type="string", enum={"draft","submitted","active","pending_renewal","terminated","canceled","expired"}),
 *     @OA\Property(property="submitted_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="activated_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="terminated_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="termination_reason", type="string", nullable=true),
 *     @OA\Property(property="start_date", type="string", format="date", nullable=true),
 *     @OA\Property(property="end_date", type="string", format="date", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 */
class ContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant' => new UserResource($this->whenLoaded('tenant')),
            'room' => new RoomResource($this->whenLoaded('room')),
            'property_status' => $this->room->property->status ?? null,
            'price_per_month' => $this->price_per_month,
            'billing_day' => $this->billing_day,
            'deposit_amount' => $this->deposit_amount,
            'grace_days' => $this->grace_days,
            'late_fee_per_day' => $this->late_fee_per_day,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'activated_at' => $this->activated_at?->toIso8601String(),
            'terminated_at' => $this->terminated_at?->toIso8601String(),
            'termination_reason' => $this->termination_reason,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
