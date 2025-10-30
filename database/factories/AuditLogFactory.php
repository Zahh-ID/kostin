<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement(['create', 'update', 'delete', 'view']),
            'entity' => fake()->randomElement(['property', 'contract', 'invoice']),
            'entity_id' => fake()->numberBetween(1, 9999),
            'meta_json' => ['ip' => fake()->ipv4()],
            'created_at' => Carbon::now(),
        ];
    }
}
