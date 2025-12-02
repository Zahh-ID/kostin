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

use function Pest\Laravel\mock;

uses(RefreshDatabase::class);

it('initiates midtrans qris payment for tenant invoice', function (): void {
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

    $midtransResponse = [
        'order_id' => sprintf('INV-%d-TEST', $invoice->id),
        'gross_amount' => '500000',
        'payment_type' => 'qris',
        'transaction_status' => 'pending',
        'qr_string' => '000201010211MIDTRANSKOSTIN',
        'actions' => [
            [
                'name' => 'generate-qr-code',
                'url' => 'https://example.com/qris/demo.png',
            ],
        ],
        'expiry_time' => now()->addMinutes(10)->toIso8601String(),
    ];

    mock(MidtransService::class, function ($mock) use ($midtransResponse, $invoice, $tenant): void {
        $mock->shouldReceive('buildQrisPayload')
            ->once()
            ->withArgs(function (string $orderId, string $description, int $amount, string $customerName, string $customerEmail) use ($invoice, $tenant): bool {
                expect(str_starts_with($orderId, 'INV-'.$invoice->id.'-'))->toBeTrue();
                expect($description)->toBe('Invoice #'.$invoice->id);
                expect($amount)->toBe($invoice->total);
                expect($customerName)->toBe($tenant->name);
                expect($customerEmail)->toBe($tenant->email);

                return true;
            })
            ->andReturn([]);

        $mock->shouldReceive('chargeQris')
            ->once()
            ->andReturn($midtransResponse);
    });

    $response = $this->actingAs($tenant, 'sanctum')
        ->postJson("/api/v1/tenant/invoices/{$invoice->id}/pay");

    $response->assertOk()->assertJsonFragment(['status' => 'pending']);

    $invoice->refresh();

    expect($invoice->external_order_id)->toBe($midtransResponse['order_id'])
        ->and($invoice->qris_payload)->toMatchArray($midtransResponse);

    $payment = Payment::where('invoice_id', $invoice->id)->first();

    expect($payment)->not->toBeNull()
        ->and($payment->payment_type)->toBe('qris')
        ->and($payment->status)->toBe('pending')
        ->and($payment->submitted_by)->toBe($tenant->id)
        ->and($payment->amount)->toBe(500_000)
        ->and($payment->raw_webhook_json)->toMatchArray($midtransResponse);

    expect(AuditLog::where('action', 'payment_initiated')->where('entity_id', $invoice->id)->exists())->toBeTrue();
});
