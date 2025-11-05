<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketEvent>
 */
class TicketEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory()->admin(),
            'event_type' => fake()->randomElement(['created', 'status_changed', 'comment_added', 'assigned', 'escalated', 'resolved', 'reopened', 'rejected']),
            'payload' => [
                'from' => fake()->randomElement(['open', 'in_review', 'escalated']),
                'to' => fake()->randomElement(['in_review', 'resolved', 'rejected']),
                'note' => fake()->sentence(),
            ],
        ];
    }
}
