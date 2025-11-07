<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'waiting_verification', 'success', 'failed', 'rejected']);
        $paymentType = fake()->randomElement(['qris', 'manual_bank_transfer', 'manual_cash']);
        $isManual = $paymentType !== 'qris';

        return [
            'invoice_id' => Invoice::factory(),
            'submitted_by' => $isManual ? User::factory()->tenant() : null,
            'user_id' => User::factory()->tenant(),
            'order_id' => Str::uuid()->toString(),
            'midtrans_order_id' => $paymentType === 'qris' ? Str::uuid()->toString() : null,
            'payment_type' => $paymentType,
            'manual_method' => $isManual ? fake()->randomElement(['BCA', 'Mandiri', 'BNI', 'Cash']) : null,
            'proof_path' => $isManual ? 'manual-payments/'.Str::uuid()->toString().'.jpg' : null,
            'proof_filename' => $isManual ? fake()->lexify('bukti-????.jpg') : null,
            'notes' => $isManual ? fake()->sentence() : null,
            'amount' => fake()->numberBetween(700000, 2500000),
            'status' => $status,
            'paid_at' => in_array($status, ['success'], true) ? Carbon::now()->subDays(fake()->numberBetween(0, 5)) : null,
            'verified_by' => in_array($status, ['success', 'rejected'], true) ? User::factory()->admin() : null,
            'verified_at' => in_array($status, ['success', 'rejected'], true) ? Carbon::now()->subDays(fake()->numberBetween(0, 3)) : null,
            'rejection_reason' => $status === 'rejected' ? fake()->sentence() : null,
            'raw_webhook_json' => $paymentType === 'qris' ? ['snap_token' => Str::uuid()->toString()] : null,
        ];
    }
}
