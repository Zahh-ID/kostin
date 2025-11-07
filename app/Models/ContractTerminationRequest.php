<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractTerminationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'tenant_id',
        'requested_end_date',
        'reason',
        'status',
        'owner_notes',
        'resolved_at',
    ];

    protected $casts = [
        'requested_end_date' => 'date',
        'resolved_at' => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
}
