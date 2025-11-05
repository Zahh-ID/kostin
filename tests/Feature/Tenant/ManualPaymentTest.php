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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('allows tenant to submit manual payment proof', function (): void {
    Storage::fake('public');

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
        'amount' => 1_500_000,
        'late_fee' => 0,
        'total' => 1_500_000,
    ]);

    PaymentAccount::factory()->create([
        'method' => 'BCA',
        'account_number' => '1234567890',
        'account_name' => 'PT KostIn',
    ]);

    $response = $this->actingAs($tenant)
        ->post(route('tenant.invoices.manual-payment.store', $invoice), [
            'payment_method' => 'BCA',
            'notes' => 'Transfer via m-banking',
            'proof' => UploadedFile::fake()->image('bukti.jpg'),
        ]);

    $response->assertRedirect(route('tenant.invoices.show', $invoice));

    $invoice->refresh();

    expect($invoice->status)->toBe('pending_verification');

    $payment = Payment::firstWhere('invoice_id', $invoice->id);

    expect($payment)->not->toBeNull()
        ->and($payment->status)->toBe('waiting_verification')
        ->and($payment->manual_method)->toBe('BCA')
        ->and(Storage::disk('public')->exists($payment->proof_path))->toBeTrue();
});
