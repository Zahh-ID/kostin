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
        $months = fake()->numberBetween(1, 3);
        $amount = fake()->numberBetween(700000, 2500000);
        $lateFee = fake()->randomElement([0, fake()->numberBetween(10000, 50000)]);
        $period = Carbon::now()->subMonths(fake()->numberBetween(0, 2))->startOfMonth();
        $coverageStart = $period->copy();
        $coverageEnd = $coverageStart->copy()->addMonths($months - 1);

        return [
            'contract_id' => Contract::factory(),
            'period_month' => $period->month,
            'period_year' => $period->year,
            'months_count' => $months,
            'coverage_start_month' => $coverageStart->month,
            'coverage_start_year' => $coverageStart->year,
            'coverage_end_month' => $coverageEnd->month,
            'coverage_end_year' => $coverageEnd->year,
            'due_date' => $period->copy()->addDays(fake()->numberBetween(0, 10)),
            'amount' => $amount,
            'late_fee' => $lateFee,
            'total' => ($amount * $months) + $lateFee,
            'status' => fake()->randomElement(['unpaid', 'paid', 'overdue', 'canceled', 'pending_verification']),
            'external_order_id' => null,
            'qris_payload' => null,
        ];
    }
}
