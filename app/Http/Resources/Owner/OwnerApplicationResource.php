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
            'property_id' => $this->property_id,
            'property_name' => $this->property?->name,
            'room_id' => $this->room_id,
            'room_name' => $this->room?->room_code,
            'tenant_notes' => $this->tenant_notes,
            // Details
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'occupants_count' => $this->occupants_count,
            'employment_status' => $this->employment_status,
            'company_name' => $this->company_name,
            'job_title' => $this->job_title,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'has_vehicle' => $this->has_vehicle,
            'vehicle_notes' => $this->vehicle_notes,
            'duration_months' => $this->duration_months,
            'preferred_start_date' => $this->preferred_start_date,
        ];
    }
}
