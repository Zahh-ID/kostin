<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\OwnerTicketUpdateRequest;
use App\Http\Resources\Owner\OwnerTicketResource;
use App\Models\Property;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketUpdateController extends Controller
{
    public function __invoke(OwnerTicketUpdateRequest $request, Ticket $ticket): JsonResponse
    {
        $owner = $request->user();

        abort_unless($this->canManage($owner?->id, $ticket), Response::HTTP_FORBIDDEN);

        // Prevent owner from updating status of tickets they reported (Platform Tickets)
        if ($ticket->reporter_id === $owner->id && $ticket->related_type === null) {
            abort(Response::HTTP_FORBIDDEN, 'Anda tidak dapat mengubah status tiket yang Anda buat untuk Admin.');
        }

        $ticket->update([
            'status' => $request->string('status'),
            'assignee_id' => $ticket->assignee_id ?: $owner?->id,
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $owner?->id,
            'body' => $request->string('notes'),
            'attachments' => [],
        ]);

        return (new OwnerTicketResource($ticket->fresh(['assignee', 'reporter'])))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    private function canManage(?int $ownerId, Ticket $ticket): bool
    {
        if ($ownerId === null) {
            return false;
        }

        if ($ticket->assignee_id === $ownerId || $ticket->reporter_id === $ownerId) {
            return true;
        }

        if ($ticket->related_type === Property::class && $ticket->related_id !== null) {
            return Property::query()
                ->whereKey($ticket->related_id)
                ->where('owner_id', $ownerId)
                ->exists();
        }

        return false;
    }
}
