<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminTicketResource;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketIndexController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $admin = $request->user();

        $tickets = Ticket::query()
            ->with(['reporter', 'assignee'])
            ->where(function ($query) {
                $query->whereNull('related_type')
                    ->orWhereNotIn('related_type', [
                        \App\Models\Property::class,
                        \App\Models\Room::class,
                        \App\Models\RoomType::class,
                        \App\Models\Contract::class,
                        \App\Models\Invoice::class,
                    ]);
            })
            ->latest()
            ->limit(50)
            ->get();

        return response()->json([
            'data' => AdminTicketResource::collection($tickets),
        ]);
    }

    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $ticket->fill(array_filter([
            'status' => $validated['status'] ?? null,
            'assignee_id' => $validated['assignee_id'] ?? null,
        ], static fn($value) => $value !== null));
        $ticket->save();

        $this->recordAudit('admin.ticket.update', 'ticket', $ticket->id, [
            'status' => $validated['status'] ?? null,
            'assignee_id' => $validated['assignee_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return (new AdminTicketResource($ticket->fresh(['reporter', 'assignee'])))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
