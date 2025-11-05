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

    $response = $this->actingAs($owner)->patch(route('owner.tickets.update', $ticket), [
        'status' => Ticket::STATUS_RESOLVED,
        'comment' => 'Teknisi sudah menyelesaikan perbaikan pada pukul 15.00 WIB.',
    ]);

    $response->assertRedirect(route('owner.tickets.show', $ticket));

    $ticket->refresh();

    expect($ticket->status)->toBe(Ticket::STATUS_RESOLVED)
        ->and($ticket->closed_at)->not->toBeNull();

    expect(TicketComment::where('ticket_id', $ticket->id)->where('body', 'Teknisi sudah menyelesaikan perbaikan pada pukul 15.00 WIB.')->exists())->toBeTrue();
    expect(TicketEvent::where('ticket_id', $ticket->id)->where('event_type', 'status_changed')->exists())->toBeTrue();
});
