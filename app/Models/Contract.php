<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class Contract extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Contract $contract): void {
            if (! $contract->tenant_id) {
                return;
            }

            $activeContract = static::query()
                ->where('tenant_id', $contract->tenant_id)
                ->where('status', 'active')
                ->first();

            if (! $activeContract) {
                return;
            }

            if ($activeContract->room_id === $contract->room_id) {
                throw ValidationException::withMessages([
                    'room_id' => __('Tenant masih memiliki kontrak aktif pada kamar ini. Akhiri kontrak lama sebelum membuat yang baru.'),
                ]);
            }

            $activeContract->status = 'ended';

            $cutoff = Carbon::parse($contract->start_date)->subDay();
            if (! $activeContract->end_date || $activeContract->end_date->gt($cutoff)) {
                $activeContract->end_date = $cutoff;
            }

            $activeContract->save();
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
}
