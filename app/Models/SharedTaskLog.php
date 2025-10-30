<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedTaskLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'shared_task_id',
        'run_at',
        'completed_by',
        'photo_url',
        'note',
    ];

    protected $casts = [
        'run_at' => 'datetime',
    ];

    public function sharedTask(): BelongsTo
    {
        return $this->belongsTo(SharedTask::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
