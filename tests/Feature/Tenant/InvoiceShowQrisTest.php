<?php

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows qris drawer details and auto-opens when flagged', function (): void {
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
        'qris_payload' => [
            'order_id' => 'INV-'.$contract->id,
            'qr_string' => 'QRIS-DEMO-STRING',
            'gross_amount' => 500_000,
            'transaction_status' => 'pending',
            'expiry_time' => now()->addMinutes(10)->toIso8601String(),
        ],
    ]);

    $response = $this->actingAs($tenant, 'sanctum')
        ->getJson("/api/v1/invoices/{$invoice->id}");

    $response->assertOk()
        ->assertJsonFragment(['id' => $invoice->id])
        ->assertJsonPath('data.qris_payload.order_id', 'INV-'.$contract->id)
        ->assertJsonPath('data.qris_payload.qr_string', 'QRIS-DEMO-STRING');
});
