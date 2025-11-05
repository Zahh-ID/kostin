<?php

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows tenant to create a support ticket', function (): void {
    $tenant = User::factory()->tenant()->create();

    $response = $this->actingAs($tenant)->post(route('tenant.tickets.store'), [
        'subject' => 'Masalah pembayaran bulan ini',
        'description' => 'Saya sudah membayar melalui transfer bank namun status masih unpaid.',
        'category' => 'payment',
        'priority' => 'high',
    ]);

    $ticket = Ticket::first();

    $response->assertRedirect(route('tenant.tickets.show', $ticket));

    expect($ticket)->not->toBeNull()
        ->and($ticket->reporter_id)->toBe($tenant->id)
        ->and($ticket->status)->toBe(Ticket::STATUS_OPEN)
        ->and($ticket->category)->toBe('payment')
        ->and($ticket->priority)->toBe('high');

    expect(TicketComment::where('ticket_id', $ticket->id)->count())->toBe(1);
    expect(TicketEvent::where('ticket_id', $ticket->id)->where('event_type', 'created')->exists())->toBeTrue();
});
