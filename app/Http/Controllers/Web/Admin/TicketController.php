<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TicketUpdateRequest;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        $tickets = Ticket::query()
            ->with([
                'reporter:id,name',
                'assignee:id,name',
            ])
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('status');

        $statuses = $this->statusLabels();

        $ownersAndAdmins = User::query()
            ->whereIn('role', [User::ROLE_ADMIN, User::ROLE_OWNER])
            ->select('id', 'name', 'role')
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('admin.tickets.index', [
            'tickets' => $tickets,
            'statuses' => $statuses,
            'assignees' => $ownersAndAdmins,
        ]);
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load([
            'reporter:id,name,email',
            'assignee:id,name,email,role',
            'comments.user:id,name,role',
            'events.user:id,name,role',
        ]);

        return view('admin.tickets.show', [
            'ticket' => $ticket,
            'statuses' => $this->statusLabels(),
            'assignees' => User::query()
                ->whereIn('role', [User::ROLE_ADMIN, User::ROLE_OWNER])
                ->select('id', 'name', 'role')
                ->orderBy('role')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(TicketUpdateRequest $request, Ticket $ticket): RedirectResponse
    {
        DB::transaction(function () use ($request, $ticket): void {
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
                    'user_id' => $request->user()->id,
                    'event_type' => 'status_changed',
                    'payload' => [
                        'from' => $ticket->status,
                        'to' => $newStatus,
                        'note' => $request->filled('comment') ? $request->string('comment') : null,
                        'note' => $request->filled('comment') ? $request->input('comment') : null,
                    ],
                ]);
            }

            if ($request->filled('assignee_id') && $ticket->assignee_id !== (int) $request->input('assignee_id')) {
                $updates['assignee_id'] = (int) $request->input('assignee_id');

                TicketEvent::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $request->user()->id,
                    'event_type' => 'assigned',
                    'payload' => [
                        'assignee_id' => (int) $request->input('assignee_id'),
                    ],
                ]);
            }

            if (! empty($updates)) {
                $ticket->update($updates);
            }

            if ($request->filled('comment')) {
                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $request->user()->id,
                    'body' => $request->input('comment'),
                    'attachments' => null,
                ]);
            }
        });

        return back()->with('status', __('Tiket diperbarui.'));
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
