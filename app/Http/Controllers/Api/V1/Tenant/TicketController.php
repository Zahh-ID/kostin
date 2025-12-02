<?php

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\TicketStoreRequest;
use App\Models\Contract;
use App\Models\Property;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketEvent;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function __construct(private readonly DatabaseManager $db)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $tenant = $request->user();

        $tickets = Ticket::query()
            ->where('reporter_id', $tenant->id)
            ->latest()
            ->limit($request->integer('limit', 10))
            ->get(['id', 'ticket_code', 'subject', 'status', 'created_at']);

        return response()->json([
            'data' => $tickets,
        ]);
    }

    public function show(Request $request, Ticket $ticket): JsonResponse
    {
        abort_unless($ticket->reporter_id === $request->user()->id, 403);

        $ticket->load([
            'assignee:id,name,role',
            'comments' => fn (Builder $query) => $query->latest()->limit(10)->with('user:id,name,role'),
        ]);

        return response()->json([
            'data' => [
                'id' => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
                'subject' => $ticket->subject,
                'description' => $ticket->description,
                'category' => $ticket->category,
                'priority' => $ticket->priority,
                'status' => $ticket->status,
                'assignee' => $ticket->assignee ? [
                    'id' => $ticket->assignee->id,
                    'name' => $ticket->assignee->name,
                    'role' => $ticket->assignee->role,
                ] : null,
                'comments' => $ticket->comments->map(fn (TicketComment $comment) => [
                    'id' => $comment->id,
                    'body' => $comment->body,
                    'created_at' => $comment->created_at,
                    'user' => [
                        'id' => $comment->user_id,
                        'name' => $comment->user?->name,
                        'role' => $comment->user?->role,
                    ],
                ]),
                'created_at' => $ticket->created_at,
            ],
        ]);
    }

    public function store(TicketStoreRequest $request): JsonResponse
    {
        $tenant = $request->user();
        $propertyId = $request->input('property_id');
        $assigneeId = null;
        $relatedType = null;
        $relatedId = null;

        if ($propertyId !== null) {
            $hasActiveContract = Contract::query()
                ->where('tenant_id', $tenant->id)
                ->whereHas('room.roomType', fn ($query) => $query->where('property_id', $propertyId))
                ->exists();

            if (! $hasActiveContract) {
                return response()->json([
                    'message' => 'Properti tidak terkait dengan kontrak Anda.',
                ], 422);
            }

            $property = Property::query()->find($propertyId);
            $assigneeId = $property?->owner_id;
            $relatedType = Property::class;
            $relatedId = $property?->id;
        }

        if ($assigneeId === null) {
            $assigneeId = User::query()
                ->where('role', User::ROLE_ADMIN)
                ->inRandomOrder()
                ->value('id');
        }

        $ticket = $this->db->transaction(function () use ($tenant, $request, $assigneeId, $relatedType, $relatedId): Ticket {
            $ticket = Ticket::create([
                'ticket_code' => $this->generateTicketCode(),
                'reporter_id' => $tenant->id,
                'subject' => $request->input('subject'),
                'description' => $request->input('description'),
                'category' => $request->input('category'),
                'priority' => $request->input('priority'),
                'status' => Ticket::STATUS_OPEN,
                'assignee_id' => $assigneeId,
                'related_type' => $relatedType,
                'related_id' => $relatedId,
            ]);

            TicketEvent::create([
                'ticket_id' => $ticket->id,
                'user_id' => $tenant->id,
                'event_type' => 'created',
                'payload' => [
                    'message' => 'Ticket created via tenant portal (API).',
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

        return response()->json([
            'data' => [
                'id' => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'created_at' => $ticket->created_at,
            ],
        ], 201);
    }

    private function generateTicketCode(): string
    {
        do {
            $code = 'TCK-'.Str::upper(Str::random(6));
        } while (Ticket::where('ticket_code', $code)->exists());

        return $code;
    }
}
