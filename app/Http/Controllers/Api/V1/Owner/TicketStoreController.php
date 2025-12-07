<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketStoreController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $owner = $request->user();

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', 'in:technical,payment,content,abuse'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
        ]);

        $ticket = DB::transaction(function () use ($owner, $validated) {
            $ticket = Ticket::create([
                'ticket_code' => $this->generateTicketCode(),
                'reporter_id' => $owner->id,
                'assignee_id' => null, // Unassigned, for Admin
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'priority' => $validated['priority'],
                'status' => Ticket::STATUS_OPEN,
                'related_type' => null, // Platform ticket
                'related_id' => null,
            ]);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $owner->id,
                'body' => $validated['description'],
            ]);

            return $ticket;
        });

        return response()->json($ticket, 201);
    }

    private function generateTicketCode(): string
    {
        do {
            $code = 'TCK-' . Str::upper(Str::random(6));
        } while (Ticket::where('ticket_code', $code)->exists());

        return $code;
    }
}
