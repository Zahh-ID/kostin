<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\RentalApplication
 */
class RentalApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'property' => new PropertyResource($this->whenLoaded('property')),
            'room_type' => new RoomTypeResource($this->whenLoaded('roomType')),
            'room' => new RoomResource($this->whenLoaded('room')),
            'preferred_start_date' => $this->preferred_start_date?->toDateString(),
            'duration_months' => $this->duration_months,
            'status' => $this->status,
            'tenant_notes' => $this->tenant_notes,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'occupants_count' => $this->occupants_count,
            'budget_per_month' => $this->budget_per_month,
            'employment_status' => $this->employment_status,
            'company_name' => $this->company_name,
            'job_title' => $this->job_title,
            'monthly_income' => $this->monthly_income,
            'has_vehicle' => $this->has_vehicle,
            'vehicle_notes' => $this->vehicle_notes,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'terms_text' => $this->terms_text,
            'terms_accepted_at' => $this->terms_accepted_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'status_reason' => $this->owner_notes,
        ];
    }
}
