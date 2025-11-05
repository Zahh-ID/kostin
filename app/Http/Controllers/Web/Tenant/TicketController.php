<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\TicketStoreRequest;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketEvent;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $tenant */
        $tenant = $request->user();

        /** @var LengthAwarePaginator $tickets */
        $tickets = $tenant->reportedTickets()
            ->with([
                'assignee:id,name,role',
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statusCounts = $tenant->reportedTickets()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('tenant.tickets.index', [
            'tickets' => $tickets,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function create(): View
    {
        return view('tenant.tickets.create', [
            'categories' => $this->categories(),
            'priorities' => $this->priorities(),
        ]);
    }

    public function store(TicketStoreRequest $request): RedirectResponse
    {
        /** @var User $tenant */
        $tenant = $request->user();

        $ticket = DB::transaction(function () use ($tenant, $request): Ticket {
            $ticket = Ticket::create([
                'ticket_code' => $this->generateTicketCode(),
                'reporter_id' => $tenant->id,
                'subject' => $request->input('subject'),
                'description' => $request->input('description'),
                'category' => $request->input('category'),
                'priority' => $request->input('priority'),
                'status' => Ticket::STATUS_OPEN,
            ]);

            TicketEvent::create([
                'ticket_id' => $ticket->id,
                'user_id' => $tenant->id,
                'event_type' => 'created',
                'payload' => [
                    'message' => 'Ticket created via tenant portal.',
                ],
            ]);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $tenant->id,
                'body' => $ticket->description,
                'attachments' => null,
            ]);

            return $ticket;
        });

        return redirect()
            ->route('tenant.tickets.show', $ticket)
            ->with('status', __('Tiket berhasil dibuat. Tim kami akan segera meninjau laporan Anda.'));
    }

    public function show(Request $request, Ticket $ticket): View
    {
        /** @var User $tenant */
        $tenant = $request->user();
        abort_if($ticket->reporter_id !== $tenant->id, 403);

        $ticket->load([
            'reporter:id,name,email',
            'assignee:id,name,role',
            'comments.user:id,name,role',
            'events' => fn ($query) => $query->latest(),
        ]);

        return view('tenant.tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function categories(): array
    {
        return [
            'technical' => __('Teknis'),
            'payment' => __('Pembayaran'),
            'content' => __('Konten'),
            'abuse' => __('Pelanggaran'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function priorities(): array
    {
        return [
            'low' => __('Rendah'),
            'medium' => __('Sedang'),
            'high' => __('Tinggi'),
            'urgent' => __('Mendesak'),
        ];
    }

    private function generateTicketCode(): string
    {
        do {
            $code = 'TCK-'.Str::upper(Str::random(6));
        } while (Ticket::where('ticket_code', $code)->exists());

        return $code;
    }
}
