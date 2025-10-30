<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['success', 'pending', 'failed']);

        return [
            'invoice_id' => Invoice::factory(),
            'midtrans_order_id' => fake()->uuid(),
            'payment_type' => 'qris',
            'amount' => fake()->numberBetween(700000, 2500000),
            'status' => $status,
            'paid_at' => $status === 'success' ? Carbon::now()->subDays(fake()->numberBetween(0, 5)) : null,
            'raw_webhook_json' => ['foo' => 'bar'],
        ];
    }
}
