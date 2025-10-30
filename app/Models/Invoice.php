<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'period_month',
        'period_year',
        'due_date',
        'amount',
        'late_fee',
        'total',
        'status',
        'external_order_id',
        'qris_payload',
    ];

    protected $casts = [
        'due_date' => 'date',
        'qris_payload' => 'array',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeCurrentPeriod($query)
    {
        $date = Carbon::now();

        return $query->where('period_month', $date->month)
            ->where('period_year', $date->year);
    }
}
