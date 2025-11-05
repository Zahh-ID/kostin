<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'is_group',
        'metadata',
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'metadata' => 'array',
    ];

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
            ->withPivot(['last_read_at', 'role']);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
