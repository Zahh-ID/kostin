<?php

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

it('allows admin to update ticket status and assignment', function (): void {
    $admin = User::factory()->admin()->create();
    $owner = User::factory()->owner()->create();
    $reporter = User::factory()->tenant()->create();

    $ticket = Ticket::factory()->create([
        'reporter_id' => $reporter->id,
        'status' => Ticket::STATUS_OPEN,
        'assignee_id' => null,
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.tickets.update', $ticket), [
        'status' => Ticket::STATUS_IN_REVIEW,
        'assignee_id' => $owner->id,
        'comment' => 'Sedang kami tinjau dan koordinasikan dengan pemilik.',
    ]);

    $response->assertRedirect();

    $ticket->refresh();

    expect($ticket->status)->toBe(Ticket::STATUS_IN_REVIEW)
        ->and($ticket->assignee_id)->toBe($owner->id);

    expect(TicketEvent::where('ticket_id', $ticket->id)->where('event_type', 'status_changed')->exists())->toBeTrue();
    expect(TicketEvent::where('ticket_id', $ticket->id)->where('event_type', 'assigned')->exists())->toBeTrue();
    expect(TicketComment::where('ticket_id', $ticket->id)->where('body', 'Sedang kami tinjau dan koordinasikan dengan pemilik.')->exists())->toBeTrue();
});

it('prevents non-admin users from updating tickets', function (): void {
    $tenant = User::factory()->tenant()->create();
    $ticket = Ticket::factory()->create();

    $response = $this->actingAs($tenant)->patch(route('admin.tickets.update', $ticket), [
        'status' => Ticket::STATUS_RESOLVED,
    ]);

    $response->assertForbidden();
});

it('sets and clears closed_at when ticket status changes', function (): void {
    $admin = User::factory()->admin()->create();
    $ticket = Ticket::factory()->create([
        'status' => Ticket::STATUS_OPEN,
        'closed_at' => null,
    ]);

    $resolvedAt = Carbon::parse('2024-11-01 12:00:00');
    Carbon::setTestNow($resolvedAt);

    $firstResponse = $this->actingAs($admin)->patch(route('admin.tickets.update', $ticket), [
        'status' => Ticket::STATUS_RESOLVED,
        'comment' => 'Masalah telah diselesaikan.',
    ]);

    $firstResponse->assertRedirect();

    $ticket->refresh();

    expect($ticket->status)->toBe(Ticket::STATUS_RESOLVED)
        ->and($ticket->closed_at)->toEqual($resolvedAt);

    expect(TicketEvent::where('ticket_id', $ticket->id)->where('event_type', 'status_changed')->count())->toBe(1);
    expect(TicketComment::where('ticket_id', $ticket->id)->where('body', 'Masalah telah diselesaikan.')->exists())->toBeTrue();

    $reopenedAt = $resolvedAt->copy()->addHour();
    Carbon::setTestNow($reopenedAt);

    $secondResponse = $this->actingAs($admin)->patch(route('admin.tickets.update', $ticket), [
        'status' => Ticket::STATUS_OPEN,
        'comment' => 'Muncul kembali, perlu ditindaklanjuti.',
    ]);

    $secondResponse->assertRedirect();

    $ticket->refresh();

    expect($ticket->status)->toBe(Ticket::STATUS_OPEN)
        ->and($ticket->closed_at)->toBeNull();

    expect(TicketEvent::where('ticket_id', $ticket->id)->where('event_type', 'status_changed')->count())->toBe(2);
    expect(TicketComment::where('ticket_id', $ticket->id)->where('body', 'Muncul kembali, perlu ditindaklanjuti.')->exists())->toBeTrue();

    Carbon::setTestNow();
});
