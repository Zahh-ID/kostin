<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        $start = Carbon::now()->subMonths(rand(0, 3))->startOfMonth();

        return [
            'tenant_id' => User::factory()->tenant(),
            'room_id' => Room::factory(),
            'start_date' => $start,
            'end_date' => null,
            'price_per_month' => fake()->numberBetween(700000, 2500000),
            'billing_day' => fake()->numberBetween(1, 28),
            'deposit_amount' => fake()->numberBetween(0, 2500000),
            'grace_days' => fake()->numberBetween(0, 7),
            'late_fee_per_day' => fake()->numberBetween(0, 50000),
            'status' => fake()->randomElement(['active', 'ended', 'canceled']),
        ];
    }
}
