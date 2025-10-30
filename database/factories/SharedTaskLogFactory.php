<?php

namespace Database\Factories;

use App\Models\SharedTask;
use App\Models\SharedTaskLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<SharedTaskLog>
 */
class SharedTaskLogFactory extends Factory
{
    protected $model = SharedTaskLog::class;

    public function definition(): array
    {
        return [
            'shared_task_id' => SharedTask::factory(),
            'run_at' => Carbon::now()->subDays(fake()->numberBetween(0, 10)),
            'completed_by' => User::factory()->tenant(),
            'photo_url' => fake()->imageUrl(),
            'note' => fake()->sentence(),
        ];
    }
}
