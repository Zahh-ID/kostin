<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'owner_id' => User::factory()->owner(),
            'name' => fake()->company().' Kost',
            'address' => fake()->address(),
            'lat' => round(fake()->latitude(-90, 90), 7),
            'lng' => round(fake()->longitude(-180, 180), 7),
            'rules_text' => fake()->paragraph(),
            'photos' => [fake()->imageUrl()],
            'status' => fake()->randomElement(['draft', 'pending', 'approved', 'rejected']),
        ];
    }
}
