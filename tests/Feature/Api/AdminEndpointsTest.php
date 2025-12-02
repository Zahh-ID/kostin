<?php

declare(strict_types=1);

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns admin dashboard metrics', function (): void {
    $admin = User::factory()->admin()->create();
    $tenant = User::factory()->tenant()->create();
    $owner = User::factory()->owner()->create();

    Property::factory()->for($owner, 'owner')->state(['status' => 'pending'])->count(2)->create();
    Ticket::factory()->create(['status' => Ticket::STATUS_OPEN, 'reporter_id' => $tenant->id]);
    Invoice::factory()->create(['status' => 'unpaid']);
    Payment::factory()->state(['status' => 'success'])->create();

    $response = $this->actingAs($admin, 'sanctum')
        ->getJson('/api/v1/admin/dashboard');

    $response->assertOk();
    $payload = $response->json();
    expect($payload['pending_moderations'])->toBeGreaterThanOrEqual(2);
    expect($payload['tickets_open'])->toBeGreaterThanOrEqual(1);
    expect($payload['users']['owner'])->toBeGreaterThanOrEqual(2);
    expect($payload)->toHaveKey('revenue_trend');
});

it('lists moderation queue for admin', function (): void {
    $admin = User::factory()->admin()->create();
    $property = Property::factory()->state(['status' => 'pending'])->create();

    $response = $this->actingAs($admin, 'sanctum')
        ->getJson('/api/v1/admin/moderations');

    $response->assertOk()->assertJsonFragment(['id' => $property->id]);

    $approve = $this->actingAs($admin, 'sanctum')
        ->postJson("/api/v1/admin/moderations/{$property->id}/approve");
    $approve->assertOk();

    $rejectProperty = Property::factory()->state(['status' => 'pending'])->create();
    $reject = $this->actingAs($admin, 'sanctum')
        ->postJson("/api/v1/admin/moderations/{$rejectProperty->id}/reject", ['moderation_notes' => 'Nope']);
    $reject->assertOk();
});

it('lists tickets and users for admin', function (): void {
    $admin = User::factory()->admin()->create();
    $ticket = Ticket::factory()->create(['assignee_id' => null]);

    $ticketResponse = $this->actingAs($admin, 'sanctum')
        ->getJson('/api/v1/admin/tickets');

    $ticketResponse->assertOk()->assertJsonFragment(['id' => $ticket->id]);

    $update = $this->actingAs($admin, 'sanctum')
        ->putJson("/api/v1/admin/tickets/{$ticket->id}", ['status' => 'resolved']);
    $update->assertOk()->assertJsonFragment(['status' => 'resolved']);

    $userResponse = $this->actingAs($admin, 'sanctum')
        ->getJson('/api/v1/admin/users');

    $userResponse->assertOk()->assertJsonFragment(['role' => $admin->role]);
});
