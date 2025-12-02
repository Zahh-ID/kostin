<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_OWNER = 'owner';

    public const ROLE_TENANT = 'tenant';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'password',
        'google_id',
        'suspended_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'suspended_at' => 'datetime',
        ];
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'tenant_id');
    }

    public function sharedTasks(): HasMany
    {
        return $this->hasMany(SharedTask::class, 'assignee_user_id');
    }

    public function sharedTaskLogs(): HasMany
    {
        return $this->hasMany(SharedTaskLog::class, 'completed_by');
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function wishlistProperties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'wishlist_items');
    }

    public function invoices(): HasManyThrough
    {
        return $this->hasManyThrough(
            Invoice::class,
            Contract::class,
            'tenant_id',
            'contract_id'
        );
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class)
            ->withTimestamps()
            ->withPivot(['last_read_at', 'role']);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function submittedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'submitted_by');
    }

    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }

    public function reportedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'reporter_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assignee_id');
    }

    public function ticketComments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function ticketEvents(): HasMany
    {
        return $this->hasMany(TicketEvent::class);
    }
}
