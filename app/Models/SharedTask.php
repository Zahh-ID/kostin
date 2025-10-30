<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SharedTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'title',
        'description',
        'rrule',
        'next_run_at',
        'assignee_user_id',
    ];

    protected $casts = [
        'next_run_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_user_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SharedTaskLog::class);
    }
}
