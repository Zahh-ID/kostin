<?php

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
});

it('allows owner to approve manual payment submissions', function (): void {
    [$owner, $tenant, $invoice, $payment] = makeManualPaymentFixture('waiting_verification', 'pending_verification');

    $response = $this->actingAs($owner)
        ->patch(route('owner.manual-payments.update', $payment), [
            'action' => 'approve',
        ]);

    $response->assertRedirect();

    $payment->refresh();
    $invoice->refresh();

    expect($payment->status)->toBe('success')
        ->and($payment->verified_by)->toBe($owner->id)
        ->and($invoice->status)->toBe('paid');
});

it('allows owner to reject manual payment submissions', function (): void {
    [$owner, $tenant, $invoice, $payment] = makeManualPaymentFixture('waiting_verification', 'pending_verification');

    $response = $this->actingAs($owner)
        ->patch(route('owner.manual-payments.update', $payment), [
            'action' => 'reject',
            'rejection_reason' => 'Bukti kurang jelas',
        ]);

    $response->assertRedirect();

    $payment->refresh();
    $invoice->refresh();

    expect($payment->status)->toBe('rejected')
        ->and($payment->rejection_reason)->toBe('Bukti kurang jelas')
        ->and($invoice->status)->toBe('unpaid');
});

/**
 * @return array{0: User,1: User,2: Invoice,3: Payment}
 */
function makeManualPaymentFixture(string $paymentStatus, string $invoiceStatus): array
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
        'status' => $invoiceStatus,
        'amount' => 1_500_000,
        'late_fee' => 0,
        'total' => 1_500_000,
    ]);

    PaymentAccount::factory()->create([
        'method' => 'BCA',
        'account_number' => '1234567890',
        'account_name' => 'PT KostIn',
    ]);

    $storedPath = 'manual-payments/'.uniqid('proof_', true).'.jpg';
    Storage::disk('public')->put($storedPath, 'fake-image');

    $payment = Payment::factory()->create([
        'invoice_id' => $invoice->id,
        'submitted_by' => $tenant->id,
        'payment_type' => 'manual_bank_transfer',
        'manual_method' => 'BCA',
        'status' => $paymentStatus,
        'proof_path' => $storedPath,
        'amount' => $invoice->total,
        'notes' => 'Transfer via ATM',
    ]);

    return [$owner, $tenant, $invoice, $payment];
}
