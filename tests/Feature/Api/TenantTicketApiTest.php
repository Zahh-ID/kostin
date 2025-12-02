<?php

declare(strict_types=1);

use App\Models\Ticket;
use App\Models\User;
use App\Models\Contract;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('lists tenant tickets', function () {
    $tenant = User::factory()->create(['role' => 'tenant']);
    Ticket::factory()->count(2)->create([
        'reporter_id' => $tenant->id,
    ]);
    Ticket::factory()->create(); // other user

    actingAs($tenant);

    $response = getJson('/api/v1/tenant/tickets?limit=5');

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(2);
});

it('creates ticket via api', function () {
    $tenant = User::factory()->create(['role' => 'tenant']);
    actingAs($tenant);

    $payload = [
        'subject' => 'Air mati',
        'description' => 'Kran tidak mengalir sejak pagi.',
        'category' => 'technical',
        'priority' => 'high',
    ];

    $response = postJson('/api/v1/tenant/tickets', $payload);

    $response->assertCreated();
    $id = $response->json('data.id');
    $this->assertDatabaseHas('tickets', [
        'id' => $id,
        'reporter_id' => $tenant->id,
        'subject' => 'Air mati',
    ]);
});

it('assigns ticket to property owner when property belongs to tenant contract', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $tenant = User::factory()->create(['role' => 'tenant']);
    $property = Property::factory()->create(['owner_id' => $owner->id]);
    $roomType = RoomType::factory()->create(['property_id' => $property->id]);
    $room = Room::factory()->create(['room_type_id' => $roomType->id, 'status' => 'available']);
    Contract::factory()->create(['tenant_id' => $tenant->id, 'room_id' => $room->id]);

    actingAs($tenant);

    $response = postJson('/api/v1/tenant/tickets', [
        'subject' => 'Lampu koridor padam',
        'description' => 'Mohon cek lampu di koridor lantai 2.',
        'category' => 'technical',
        'priority' => 'medium',
        'property_id' => $property->id,
    ]);

    $response->assertCreated();
    $ticketId = $response->json('data.id');

    $this->assertDatabaseHas('tickets', [
        'id' => $ticketId,
        'reporter_id' => $tenant->id,
        'assignee_id' => $owner->id,
        'related_type' => Property::class,
        'related_id' => $property->id,
    ]);
});

it('rejects ticket creation for unrelated property', function () {
    $tenant = User::factory()->create(['role' => 'tenant']);
    $owner = User::factory()->create(['role' => 'owner']);
    $property = Property::factory()->create(['owner_id' => $owner->id]);

    actingAs($tenant);

    $response = postJson('/api/v1/tenant/tickets', [
        'subject' => 'Coba akses',
        'description' => 'Harus ditolak.',
        'category' => 'technical',
        'priority' => 'low',
        'property_id' => $property->id,
    ]);

    $response->assertStatus(422);
});

it('shows a ticket detail for owner reporter only', function () {
    $tenant = User::factory()->create(['role' => 'tenant']);
    $other = User::factory()->create(['role' => 'tenant']);
    $ticket = Ticket::factory()->create(['reporter_id' => $tenant->id]);

    actingAs($tenant);
    $ok = getJson("/api/v1/tenant/tickets/{$ticket->id}");
    $ok->assertSuccessful();

    actingAs($other);
    $forbidden = getJson("/api/v1/tenant/tickets/{$ticket->id}");
    $forbidden->assertForbidden();
});
