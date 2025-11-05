<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $amount = fake()->numberBetween(700000, 2500000);
        $lateFee = fake()->randomElement([0, fake()->numberBetween(10000, 50000)]);
        $period = Carbon::now()->subMonths(fake()->numberBetween(0, 2))->startOfMonth();

        return [
            'contract_id' => Contract::factory(),
            'period_month' => $period->month,
            'period_year' => $period->year,
            'due_date' => $period->copy()->addDays(fake()->numberBetween(0, 10)),
            'amount' => $amount,
            'late_fee' => $lateFee,
            'total' => $amount + $lateFee,
            'status' => fake()->randomElement(['unpaid', 'paid', 'overdue', 'canceled', 'pending_verification']),
            'external_order_id' => null,
            'qris_payload' => null,
        ];
    }
}
