<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'submitted_by',
        'user_id',
        'order_id',
        'midtrans_order_id',
        'payment_type',
        'manual_method',
        'proof_path',
        'proof_filename',
        'notes',
        'amount',
        'status',
        'paid_at',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'raw_webhook_json',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'raw_webhook_json' => 'array',
    ];

    /**
     * Relationships
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Check if payment is successful
     */
    public function isSuccess()
    {
        return $this->status === 'success';
    }

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
}
