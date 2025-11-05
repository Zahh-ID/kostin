<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentAccount>
 */
class PaymentAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $method = fake()->unique()->randomElement([
            'BCA',
            'Mandiri',
            'BNI',
            'BRI',
            'Permata',
            'CIMB Niaga',
            'Danamon',
            'BTN',
        ]);

        return [
            'method' => $method,
            'account_number' => fake()->numerify('##########'),
            'account_name' => fake()->company(),
            'instructions' => fake()->optional()->sentence(),
            'metadata' => [
                'branch' => fake()->city(),
                'swift_code' => fake()->swiftBicNumber(),
            ],
            'is_active' => true,
            'display_order' => fake()->numberBetween(0, 20),
        ];
    }
}
