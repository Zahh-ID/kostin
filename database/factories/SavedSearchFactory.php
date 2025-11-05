<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SavedSearch>
 */
class SavedSearchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->tenant(),
            'name' => fake()->sentence(3),
            'filters' => [
                'search' => fake()->word(),
                'city' => fake()->randomElement(['Jakarta', 'Bogor', 'Bandung', 'Depok']),
                'type' => fake()->randomElement(['putra', 'putri', 'campur']),
                'minPrice' => fake()->numberBetween(500_000, 1_000_000),
                'maxPrice' => fake()->numberBetween(1_500_000, 3_500_000),
                'facilities' => fake()->randomElements(['ac', 'wifi', 'laundry', 'parking'], 2),
            ],
            'notification_enabled' => fake()->boolean(),
            'last_notified_at' => fake()->optional()->dateTimeBetween('-1 month'),
        ];
    }
}
