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

    public const STATUS_UNPAID = 'unpaid';

    public const STATUS_PAID = 'paid';

    public const STATUS_OVERDUE = 'overdue';

    public const STATUS_CANCELED = 'canceled';

    public const STATUS_PENDING_VERIFICATION = 'pending_verification';

    public const STATUS_EXPIRED = 'expired';

    public const OUTSTANDING_STATUSES = [
        self::STATUS_UNPAID,
        self::STATUS_OVERDUE,
        self::STATUS_PENDING_VERIFICATION,
        self::STATUS_EXPIRED,
    ];

    protected $fillable = [
        'contract_id',
        'period_month',
        'period_year',
        'months_count',
        'coverage_start_month',
        'coverage_start_year',
        'coverage_end_month',
        'coverage_end_year',
        'due_date',
        'amount',
        'late_fee',
        'total',
        'status',
        'status_reason',
        'expires_at',
        'primary_payment_id',
        'external_order_id',
        'qris_payload',
        'paid_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'qris_payload' => 'array',
        'months_count' => 'integer',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeOutstanding($query)
    {
        return $query->whereIn('status', self::OUTSTANDING_STATUSES);
    }

    public function scopeCurrentPeriod($query)
    {
        $date = Carbon::now();

        return $query->where('period_month', $date->month)
            ->where('period_year', $date->year);
    }

    public function coverageStart(): Carbon
    {
        $month = $this->coverage_start_month ?? $this->period_month;
        $year = $this->coverage_start_year ?? $this->period_year;

        return Carbon::create($year, $month, 1)->startOfMonth();
    }

    public function coverageEnd(): Carbon
    {
        if ($this->coverage_end_month && $this->coverage_end_year) {
            return Carbon::create($this->coverage_end_year, $this->coverage_end_month, 1)->startOfMonth();
        }

        return $this->coverageStart()->copy()->addMonths(max(1, $this->months_count ?? 1) - 1);
    }

    public function markAsPaid(): void
    {
        if ($this->status === 'paid') {
            return;
        }

        $this->forceFill([
            'status' => 'paid',
            'paid_at' => now(),
        ])->save();

        $contract = $this->contract;

        if (! $contract) {
            return;
        }

        $newEnd = $this->coverageEnd()->copy()->endOfMonth();

        if (! $contract->end_date || $newEnd->gt($contract->end_date)) {
            $contract->end_date = $newEnd;
            $contract->save();
        }
    }
}
