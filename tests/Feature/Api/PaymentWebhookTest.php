<?php

use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

use function Pest\Laravel\mock;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'services.midtrans.server_key' => 'mid-server-key',
        'services.midtrans.is_production' => false,
    ]);
});

it('rejects webhook requests with invalid signature', function (): void {
    $payload = [
        'order_id' => 'INV-999-TEST',
        'transaction_status' => 'settlement',
        'payment_type' => 'qris',
        'gross_amount' => '500000',
        'status_code' => '200',
        'signature_key' => 'invalid-signature',
    ];

    mock(MidtransService::class, function ($mock) use ($payload): void {
        $mock->shouldReceive('normalizeNotification')
            ->once()
            ->with($payload)
            ->andReturn([
                'order_id' => $payload['order_id'],
                'transaction_status' => $payload['transaction_status'],
                'payment_type' => $payload['payment_type'],
                'gross_amount' => 500000,
                'status_code' => $payload['status_code'],
                'signature_key' => $payload['signature_key'],
            ]);

        $mock->shouldReceive('isValidSignature')
            ->once()
            ->with($payload['signature_key'], $payload['order_id'], $payload['status_code'], '500000')
            ->andReturnFalse();
    });

    $response = $this->postJson(route('midtrans.webhook'), $payload);

    $response->assertStatus(400)
        ->assertJson([
            'message' => 'Invalid signature.',
        ]);

    expect(Payment::count())->toBe(0);
});

it('returns not found when payment record cannot be located', function (): void {
    $payload = [
        'order_id' => 'INV-404',
        'transaction_status' => 'pending',
        'payment_type' => 'qris',
        'gross_amount' => '250000',
        'status_code' => '201',
        'signature_key' => 'valid-signature',
    ];

    mock(MidtransService::class, function ($mock) use ($payload): void {
        $mock->shouldReceive('normalizeNotification')
            ->once()
            ->with($payload)
            ->andReturn([
                'order_id' => $payload['order_id'],
                'transaction_status' => $payload['transaction_status'],
                'payment_type' => $payload['payment_type'],
                'gross_amount' => 250000,
                'status_code' => $payload['status_code'],
                'signature_key' => $payload['signature_key'],
            ]);

        $mock->shouldReceive('isValidSignature')
            ->once()
            ->with($payload['signature_key'], $payload['order_id'], $payload['status_code'], '250000')
            ->andReturnTrue();
    });

    $response = $this->postJson(route('midtrans.webhook'), $payload);

    $response->assertNotFound()
        ->assertJson([
            'message' => 'Payment not found.',
        ]);
});

it('marks the payment and invoice as settled when webhook confirms settlement', function (): void {
    [$invoice, $payment] = createQrisPaymentFixture();

    $payload = [
        'order_id' => $payment->midtrans_order_id,
        'transaction_status' => 'settlement',
        'payment_type' => 'qris',
        'gross_amount' => (string) $payment->amount,
        'status_code' => '200',
        'signature_key' => 'valid-signature',
    ];

    $normalized = [
        'order_id' => $payment->midtrans_order_id,
        'transaction_status' => 'settlement',
        'payment_type' => 'qris',
        'gross_amount' => $payment->amount,
        'status_code' => '200',
        'signature_key' => 'valid-signature',
    ];

    mock(MidtransService::class, function ($mock) use ($payload, $normalized): void {
        $mock->shouldReceive('normalizeNotification')
            ->once()
            ->with($payload)
            ->andReturn($normalized);

        $mock->shouldReceive('isValidSignature')
            ->once()
            ->with($normalized['signature_key'], $normalized['order_id'], $normalized['status_code'], (string) $normalized['gross_amount'])
            ->andReturnTrue();
    });

    $now = Carbon::parse('2024-11-05 11:37:21');
    Carbon::setTestNow($now);

    $response = $this->postJson(route('midtrans.webhook'), $payload);

    Carbon::setTestNow();

    $response->assertOk();

    $payment->refresh();
    $invoice->refresh();

    expect($payment->status)->toBe('success')
        ->and($payment->paid_at)->toEqual($now)
        ->and($payment->raw_webhook_json)->toMatchArray($payload)
        ->and($invoice->status)->toBe('paid');

    expect(AuditLog::where('action', 'payment_webhook')->where('entity_id', $invoice->id)->exists())->toBeTrue();
});

it('marks the payment as failed and invoice overdue when webhook expires the transaction', function (): void {
    [$invoice, $payment] = createQrisPaymentFixture();

    $payload = [
        'order_id' => $payment->midtrans_order_id,
        'transaction_status' => 'expire',
        'payment_type' => 'qris',
        'gross_amount' => (string) $payment->amount,
        'status_code' => '407',
        'signature_key' => 'valid-signature',
    ];

    $normalized = [
        'order_id' => $payment->midtrans_order_id,
        'transaction_status' => 'expire',
        'payment_type' => 'qris',
        'gross_amount' => $payment->amount,
        'status_code' => '407',
        'signature_key' => 'valid-signature',
    ];

    mock(MidtransService::class, function ($mock) use ($payload, $normalized): void {
        $mock->shouldReceive('normalizeNotification')
            ->once()
            ->with($payload)
            ->andReturn($normalized);

        $mock->shouldReceive('isValidSignature')
            ->once()
            ->with($normalized['signature_key'], $normalized['order_id'], $normalized['status_code'], (string) $normalized['gross_amount'])
            ->andReturnTrue();
    });

    $response = $this->postJson(route('midtrans.webhook'), $payload);

    $response->assertOk();

    $payment->refresh();
    $invoice->refresh();

    expect($payment->status)->toBe('failed')
        ->and($payment->paid_at)->toBeNull()
        ->and($invoice->status)->toBe('overdue')
        ->and($payment->raw_webhook_json)->toMatchArray($payload);

    expect(AuditLog::where('action', 'payment_webhook')->where('entity_id', $invoice->id)->exists())->toBeTrue();
});

/**
 * @return array{0: Invoice, 1: Payment}
 */
function createQrisPaymentFixture(): array
{
    $owner = User::factory()->owner()->create();
    $tenant = User::factory()->tenant()->create();

    $property = Property::factory()->create([
        'owner_id' => $owner->id,
        'status' => 'approved',
    ]);

    $roomType = RoomType::factory()->create(['property_id' => $property->id]);
    $room = Room::factory()->create(['room_type_id' => $roomType->id]);

    $contract = Contract::factory()->create([
        'tenant_id' => $tenant->id,
        'room_id' => $room->id,
        'status' => 'active',
    ]);

    $invoice = Invoice::factory()->create([
        'contract_id' => $contract->id,
        'status' => 'unpaid',
        'amount' => 500_000,
        'late_fee' => 0,
        'total' => 500_000,
    ]);

    $payment = Payment::factory()->create([
        'invoice_id' => $invoice->id,
        'payment_type' => 'qris',
        'midtrans_order_id' => 'INV-'.$invoice->id.'-ORDER',
        'status' => 'pending',
        'amount' => $invoice->total,
        'raw_webhook_json' => null,
        'paid_at' => null,
    ]);

    return [$invoice, $payment];
}
