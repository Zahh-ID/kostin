<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement([
            Ticket::STATUS_OPEN,
            Ticket::STATUS_IN_REVIEW,
            Ticket::STATUS_ESCALATED,
            Ticket::STATUS_RESOLVED,
            Ticket::STATUS_REJECTED,
        ]);

        return [
            'ticket_code' => 'TCK-'.Str::upper(Str::random(6)),
            'reporter_id' => User::factory()->tenant(),
            'assignee_id' => fake()->boolean(70) ? User::factory()->admin() : null,
            'subject' => fake()->sentence(6),
            'description' => fake()->paragraphs(2, true),
            'category' => fake()->randomElement(['technical', 'payment', 'content', 'abuse']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => $status,
            'tags' => fake()->randomElements(['urgent', 'invoice', 'maintenance', 'chat', 'manual_payment'], 2),
            'sla_minutes' => fake()->optional()->numberBetween(60, 720),
            'closed_at' => in_array($status, [Ticket::STATUS_RESOLVED, Ticket::STATUS_REJECTED], true)
                ? fake()->dateTimeBetween('-10 days', '-1 day')
                : null,
            'escalated_at' => $status === Ticket::STATUS_ESCALATED
                ? fake()->dateTimeBetween('-5 days', 'now')
                : null,
        ];
    }
}
