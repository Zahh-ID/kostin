<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'area_m2',
        'bathroom_type',
        'base_price',
        'deposit',
        'facilities_json',
        'description',
    ];

    protected $casts = [
        'facilities_json' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
