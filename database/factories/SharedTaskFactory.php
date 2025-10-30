<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\SharedTask;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<SharedTask>
 */
class SharedTaskFactory extends Factory
{
    protected $model = SharedTask::class;

    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'title' => 'Task '.fake()->word(),
            'description' => fake()->sentence(),
            'rrule' => 'FREQ=WEEKLY;BYDAY=MO',
            'next_run_at' => Carbon::now()->addDays(fake()->numberBetween(1, 7)),
            'assignee_user_id' => User::factory()->owner(),
        ];
    }
}
