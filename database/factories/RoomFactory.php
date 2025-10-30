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
        return [
            'room_type_id' => RoomType::factory(),
            'room_code' => (string) fake()->unique()->numerify('10#'),
            'custom_price' => null,
            'status' => fake()->randomElement(['available', 'occupied', 'maintenance']),
            'facilities_override_json' => null,
        ];
    }
}
