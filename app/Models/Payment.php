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
        'midtrans_order_id',
        'payment_type',
        'amount',
        'status',
        'paid_at',
        'raw_webhook_json',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'raw_webhook_json' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
