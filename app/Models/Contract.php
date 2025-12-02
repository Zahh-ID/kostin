<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $activated_at
 * @property \Illuminate\Support\Carbon|null $terminated_at
 */
class Contract extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PENDING_RENEWAL = 'pending_renewal';

    public const STATUS_TERMINATED = 'terminated';

    public const STATUS_CANCELED = 'canceled';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'tenant_id',
        'room_id',
        'start_date',
        'end_date',
        'price_per_month',
        'billing_day',
        'deposit_amount',
        'grace_days',
        'late_fee_per_day',
        'status',
        'submitted_at',
        'activated_at',
        'terminated_at',
        'termination_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'submitted_at' => 'datetime',
        'activated_at' => 'datetime',
        'terminated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Contract $contract): void {
            if ($contract->status !== self::STATUS_ACTIVE) {
                return;
            }

            if (!$contract->tenant_id) {
                return;
            }

            $activeContract = static::query()
                ->where('tenant_id', $contract->tenant_id)
                ->where('status', self::STATUS_ACTIVE)
                ->first();

            if (!$activeContract) {
                return;
            }

            if ($activeContract->room_id === $contract->room_id) {
                throw ValidationException::withMessages([
                    'room_id' => __('Tenant masih memiliki kontrak aktif pada kamar ini. Akhiri kontrak lama sebelum membuat yang baru.'),
                ]);
            }

            $activeContract->status = self::STATUS_TERMINATED;
            $activeContract->terminated_at = now();

            $cutoff = Carbon::parse($contract->start_date)->subDay();
            if (!$activeContract->end_date || $activeContract->end_date->gt($cutoff)) {
                $activeContract->end_date = $cutoff;
            }

            $activeContract->save();
        });

        static::updated(function (Contract $contract): void {
            if ($contract->wasChanged('status') && $contract->status === self::STATUS_ACTIVE) {
                $contract->deactivateOtherContracts();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function outstandingInvoices(): HasMany
    {
        return $this->invoices()->whereIn('status', Invoice::OUTSTANDING_STATUSES);
    }

    public function hasOutstandingInvoices(): bool
    {
        if ($this->relationLoaded('invoices')) {
            return $this->invoices->contains(function (Invoice $invoice) {
                return in_array($invoice->status, Invoice::OUTSTANDING_STATUSES, true);
            });
        }

        return $this->outstandingInvoices()->exists();
    }

    public function outstandingInvoicesCount(): int
    {
        if ($this->relationLoaded('invoices')) {
            return $this->invoices->filter(function (Invoice $invoice) {
                return in_array($invoice->status, Invoice::OUTSTANDING_STATUSES, true);
            })->count();
        }

        return $this->outstandingInvoices()->count();
    }

    public function terminationRequests(): HasMany
    {
        return $this->hasMany(ContractTerminationRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function deactivateOtherContracts(): void
    {
        if (!$this->tenant_id) {
            return;
        }

        $cutoff = $this->start_date ? Carbon::parse($this->start_date)->subDay() : now();

        static::query()
            ->where('tenant_id', $this->tenant_id)
            ->where('id', '!=', $this->id)
            ->where('status', self::STATUS_ACTIVE)
            ->get()
            ->each(function (Contract $activeContract) use ($cutoff): void {
                $activeContract->status = self::STATUS_TERMINATED;
                $activeContract->terminated_at = now();
                if (!$activeContract->end_date || $activeContract->end_date->gt($cutoff)) {
                    $activeContract->end_date = $cutoff;
                }
                $activeContract->save();
            });
    }
}
