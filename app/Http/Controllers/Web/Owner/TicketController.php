<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\TicketUpdateRequest;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketEvent;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $owner */
        $owner = $request->user();

        /** @var LengthAwarePaginator $tickets */
        $tickets = Ticket::query()
            ->where('assignee_id', $owner->id)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->with([
                'reporter:id,name,email',
            ])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('owner.tickets.index', [
            'tickets' => $tickets,
            'statuses' => $this->statusLabels(),
        ]);
    }

    public function show(Request $request, Ticket $ticket): View
    {
        /** @var User $owner */
        $owner = $request->user();
        abort_if($ticket->assignee_id !== $owner->id, 403);

        $ticket->load([
            'reporter:id,name,email',
            'assignee:id,name,email',
            'comments.user:id,name,role',
            'events.user:id,name,role',
        ]);

        return view('owner.tickets.show', [
            'ticket' => $ticket,
            'statuses' => $this->statusLabels(),
        ]);
    }

    public function update(TicketUpdateRequest $request, Ticket $ticket): RedirectResponse
    {
        /** @var User $owner */
        $owner = $request->user();
        abort_if($ticket->assignee_id !== $owner->id, 403);

        DB::transaction(function () use ($request, $ticket, $owner): void {
            $updates = [];
            $newStatus = $request->input('status');

            if ($ticket->status !== $newStatus) {
                $updates['status'] = $newStatus;

                if ($newStatus === Ticket::STATUS_RESOLVED) {
                    $updates['closed_at'] = now();
                } elseif ($ticket->closed_at !== null) {
                    $updates['closed_at'] = null;
                }

                TicketEvent::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $owner->id,
                    'event_type' => 'status_changed',
                    'payload' => [
                        'from' => $ticket->status,
                        'to' => $newStatus,
                        'note' => $request->filled('comment') ? $request->input('comment') : null,
                    ],
                ]);
            }

            if (! empty($updates)) {
                $ticket->update($updates);
            }

            if ($request->filled('comment')) {
                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $owner->id,
                    'body' => $request->input('comment'),
                    'attachments' => null,
                ]);
            }
        });

        return redirect()
            ->route('owner.tickets.show', $ticket)
            ->with('status', __('Status tiket diperbarui.'));
    }

    /**
     * @return array<string, string>
     */
    private function statusLabels(): array
    {
        return [
            Ticket::STATUS_OPEN => __('Open'),
            Ticket::STATUS_IN_REVIEW => __('In Review'),
            Ticket::STATUS_ESCALATED => __('Escalated'),
            Ticket::STATUS_RESOLVED => __('Resolved'),
            Ticket::STATUS_REJECTED => __('Rejected'),
        ];
    }
}
