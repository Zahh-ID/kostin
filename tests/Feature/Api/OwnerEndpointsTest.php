<?php

declare(strict_types=1);

use App\Models\Contract;
use App\Models\ContractTerminationRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Ticket;
use App\Models\TicketComment;

it('returns only owner properties with status counts', function (): void {
    $owner = User::factory()->owner()->create();
    $otherOwner = User::factory()->owner()->create();

    $approved = Property::factory()->for($owner, 'owner')->state(['status' => 'approved'])->create();
    Property::factory()->for($owner, 'owner')->state(['status' => 'pending'])->create();
    Property::factory()->for($owner, 'owner')->state(['status' => 'draft'])->create();
    $foreign = Property::factory()->for($otherOwner, 'owner')->state(['status' => 'approved'])->create();

    $response = $this->actingAs($owner, 'sanctum')
        ->getJson('/api/v1/owner/properties');

    $response->assertOk();
    $response->assertJsonFragment(['id' => $approved->id]);
    $response->assertJsonMissing(['id' => $foreign->id]);
    $response->assertJsonPath('meta.counts.approved', 1);
    $response->assertJsonPath('meta.counts.pending', 1);
    $response->assertJsonPath('meta.counts.draft', 1);
});

it('returns contracts and termination requests scoped to owner', function (): void {
    $owner = User::factory()->owner()->create();
    $tenant = User::factory()->tenant()->create();
    $otherOwner = User::factory()->owner()->create();

    $property = Property::factory()->for($owner, 'owner')->state(['status' => 'approved'])->create();
    $roomType = RoomType::factory()->for($property)->create();
    $room = Room::factory()->for($roomType)->create();
    $contract = Contract::factory()->for($tenant, 'tenant')->for($room)->state(['status' => 'active'])->create();
    ContractTerminationRequest::query()->create([
        'contract_id' => $contract->id,
        'tenant_id' => $tenant->id,
        'status' => 'pending',
        'requested_end_date' => now()->addDays(30),
    ]);

    $otherProperty = Property::factory()->for($otherOwner, 'owner')->state(['status' => 'approved'])->create();
    $otherRoomType = RoomType::factory()->for($otherProperty)->create();
    $otherRoom = Room::factory()->for($otherRoomType)->create();
    Contract::factory()->for(User::factory()->tenant(), 'tenant')->for($otherRoom)->create();

    $response = $this->actingAs($owner, 'sanctum')
        ->getJson('/api/v1/owner/contracts');

    $response->assertOk();
    $response->assertJsonPath('contracts.0.room.property', $property->name);
    $response->assertJsonCount(1, 'contracts');
    $response->assertJsonCount(1, 'termination_requests');
});

it('returns manual payments and wallet scoped to owner', function (): void {
    $owner = User::factory()->owner()->create();
    $tenant = User::factory()->tenant()->create();
    $property = Property::factory()->for($owner, 'owner')->state(['status' => 'approved'])->create();
    $roomType = RoomType::factory()->for($property)->create();
    $room = Room::factory()->for($roomType)->create();
    $contract = Contract::factory()->for($tenant, 'tenant')->for($room)->state(['status' => 'active'])->create();
    $invoice = Invoice::factory()->for($contract)->create(['status' => 'pending_verification']);
    $payment = Payment::factory()->for($invoice)->state([
        'status' => 'pending',
        'payment_type' => 'manual_bank_transfer',
        'manual_method' => 'BCA',
        'amount' => 1500000,
        'submitted_by' => $tenant->id,
    ])->create();

    $this->actingAs($owner, 'sanctum')
        ->getJson('/api/v1/owner/manual-payments')
        ->assertOk()
        ->assertJsonFragment(['id' => $payment->id])
        ->assertJsonPath('data.0.contract.property', $property->name);

    $walletResponse = $this->actingAs($owner, 'sanctum')
        ->getJson('/api/v1/owner/wallet');

    $walletResponse->assertOk();
    $walletResponse->assertJsonPath('meta.pending_settlement', 1500000);
});

it('allows owner to create, update, submit, and withdraw property via api', function (): void {
    $owner = User::factory()->owner()->create();

    $payload = [
        'name' => 'Kos API',
        'address' => 'Jl. API No.1',
        'lat' => -6.2,
        'lng' => 106.8,
        'rules_text' => 'No pets',
        'photos' => ['https://example.com/photo.jpg'],
    ];

    $create = $this->actingAs($owner, 'sanctum')
        ->postJson('/api/v1/owner/properties', $payload);

    $create->assertCreated();
    $create->assertJson(fn (AssertableJson $json) => $json
        ->where('data.status', 'draft')
        ->where('data.name', 'Kos API')
    );

    $propertyId = $create->json('data.id');

    $update = $this->actingAs($owner, 'sanctum')
        ->putJson("/api/v1/owner/properties/{$propertyId}", [
            ...$payload,
            'name' => 'Kos API Edit',
        ]);

    $update->assertOk()->assertJsonPath('data.name', 'Kos API Edit');

    $submit = $this->actingAs($owner, 'sanctum')
        ->postJson("/api/v1/owner/properties/{$propertyId}/submit");

    $submit->assertOk()->assertJsonPath('data.status', 'pending');

    $withdraw = $this->actingAs($owner, 'sanctum')
        ->postJson("/api/v1/owner/properties/{$propertyId}/withdraw");

    $withdraw->assertOk()->assertJsonPath('data.status', 'draft');
});

it('allows owner to upload property photo', function (): void {
    Storage::fake('public');
    $owner = User::factory()->owner()->create();
    $property = Property::factory()->for($owner, 'owner')->create();

    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQImWNgYGD4DwABBAEAffh0uQAAAABJRU5ErkJggg==');

    $response = $this->actingAs($owner, 'sanctum')
        ->postJson("/api/v1/owner/properties/{$property->id}/photos", [
            'photo' => UploadedFile::fake()->createWithContent('photo.png', $png),
        ]);

    $response->assertOk();
    $path = $response->json('path');
    Storage::disk('public')->assertExists($path);
    expect($response->json('property.photos'))->toContain(Storage::disk('public')->url($path));
});

it('allows owner to create room under own property', function (): void {
    $owner = User::factory()->owner()->create();
    $property = Property::factory()->for($owner, 'owner')->create();
    $roomType = RoomType::factory()->for($property)->create();

    $response = $this->actingAs($owner, 'sanctum')
        ->postJson('/api/v1/owner/rooms', [
            'room_type_id' => $roomType->id,
            'room_code' => 'A01',
            'custom_price' => 1500000,
            'status' => 'available',
            'description' => 'Kamar baru',
        ]);

    $response->assertCreated()->assertJson(fn (AssertableJson $json) => $json
        ->where('data.room_type.id', $roomType->id)
        ->where('data.room_type.property', $property->name)
        ->where('data.status', 'available')
    );
});

it('allows owner to update ticket assigned to them or their property', function (): void {
    $owner = User::factory()->owner()->create();
    $tenant = User::factory()->tenant()->create();
    $property = Property::factory()->for($owner, 'owner')->create();
    $ticket = Ticket::factory()->create([
        'reporter_id' => $tenant->id,
        'assignee_id' => $owner->id,
        'related_type' => Property::class,
        'related_id' => $property->id,
        'status' => Ticket::STATUS_OPEN,
    ]);

    $response = $this->actingAs($owner, 'sanctum')
        ->putJson("/api/v1/owner/tickets/{$ticket->id}", [
            'status' => Ticket::STATUS_RESOLVED,
            'notes' => 'Sudah diperbaiki oleh teknisi.',
        ]);

    $response->assertOk();
    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'status' => Ticket::STATUS_RESOLVED,
    ]);

    $this->assertDatabaseHas('ticket_comments', [
        'ticket_id' => $ticket->id,
        'user_id' => $owner->id,
        'body' => 'Sudah diperbaiki oleh teknisi.',
    ]);
});

it('forbids owner updating tickets not related to them', function (): void {
    $owner = User::factory()->owner()->create();
    $otherOwner = User::factory()->owner()->create();
    $ticket = Ticket::factory()->create(['assignee_id' => $otherOwner->id]);

    $response = $this->actingAs($owner, 'sanctum')
        ->putJson("/api/v1/owner/tickets/{$ticket->id}", [
            'status' => Ticket::STATUS_RESOLVED,
            'notes' => 'Coba ubah',
        ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('ticket_comments', [
        'ticket_id' => $ticket->id,
        'body' => 'Coba ubah',
    ]);
});
