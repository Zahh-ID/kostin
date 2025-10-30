<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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

    public function invoices(): HasManyThrough
    {
        return $this->hasManyThrough(
            Invoice::class,
            Contract::class,
            'tenant_id',
            'contract_id'
        );
    }
}
