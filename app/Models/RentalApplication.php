<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'property_id',
        'room_type_id',
        'room_id',
        'preferred_start_date',
        'duration_months',
        'status',
        'tenant_notes',
        'owner_notes',
        'approved_at',
        'rejected_at',
        'terms_text',
        'terms_accepted_at',
        'contact_phone',
        'contact_email',
        'occupants_count',
        'budget_per_month',
        'employment_status',
        'company_name',
        'job_title',
        'monthly_income',
        'has_vehicle',
        'vehicle_notes',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'preferred_start_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'terms_accepted_at' => 'datetime',
        'has_vehicle' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
