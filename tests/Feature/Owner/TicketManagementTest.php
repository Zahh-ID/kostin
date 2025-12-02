<?php

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows owner to update ticket status with comment', function (): void {
    $owner = User::factory()->owner()->create();
    $tenant = User::factory()->tenant()->create();

    $ticket = Ticket::factory()->create([
        'reporter_id' => $tenant->id,
        'assignee_id' => $owner->id,
        'status' => Ticket::STATUS_IN_REVIEW,
    ]);

    $response = $this->actingAs($owner, 'sanctum')->patchJson("/api/v1/owner/tickets/{$ticket->id}", [
        'status' => Ticket::STATUS_RESOLVED,
        'comment' => 'Teknisi sudah menyelesaikan perbaikan pada pukul 15.00 WIB.',
        'notes' => 'Selesai',
    ]);

    $response->assertOk();

    $ticket->refresh();

    expect($ticket->status)->toBe(Ticket::STATUS_RESOLVED);
});
