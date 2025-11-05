<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAccount extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentAccountFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'method',
        'account_number',
        'account_name',
        'instructions',
        'metadata',
        'is_active',
        'display_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }
}
