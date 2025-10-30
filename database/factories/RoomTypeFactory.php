<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomType>
 */
class RoomTypeFactory extends Factory
{
    protected $model = RoomType::class;

    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'name' => 'Tipe '.fake()->randomElement(['Standard', 'Deluxe', 'Premium']),
            'area_m2' => fake()->numberBetween(12, 24),
            'bathroom_type' => fake()->randomElement(['inside', 'outside']),
            'base_price' => fake()->numberBetween(500000, 2000000),
            'deposit' => fake()->numberBetween(0, 2000000),
            'facilities_json' => [
                'wifi' => true,
                'ac' => fake()->boolean(),
            ],
        ];
    }
}
