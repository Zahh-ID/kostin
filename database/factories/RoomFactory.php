<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        $price = fake()->numberBetween(800000, 2500000);

        return [
            'room_type_id' => RoomType::factory(),
            'room_code' => 'RM-'.fake()->numerify('###').fake()->randomLetter(),
            'custom_price' => $price,
            'status' => fake()->randomElement(['available', 'occupied', 'maintenance']),
            'facilities_override_json' => null,
            'description' => fake()->sentences(3, true),
            'photos_json' => [
                'https://via.placeholder.com/960x640.png?text=Kamar+'.fake()->numerify('###'),
            ],
        ];
    }
}
