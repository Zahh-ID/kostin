<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory;

    public const STATUS_OPEN = 'open';

    public const STATUS_IN_REVIEW = 'in_review';

    public const STATUS_ESCALATED = 'escalated';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_REJECTED = 'rejected';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ticket_code',
        'reporter_id',
        'assignee_id',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'related_type',
        'related_id',
        'tags',
        'sla_minutes',
        'closed_at',
        'escalated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'closed_at' => 'datetime',
            'escalated_at' => 'datetime',
            'sla_minutes' => 'integer',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany<TicketComment>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    /**
     * @return HasMany<TicketEvent>
     */
    public function events(): HasMany
    {
        return $this->hasMany(TicketEvent::class);
    }
}
