<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\Owner\OwnerTicketResource;
use App\Models\Property;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketIndexController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $owner */
        $owner = $request->user();

        $tickets = Ticket::query()
            ->with(['assignee', 'reporter'])
            ->where(function ($query) use ($owner, $request) {
                $type = $request->input('type', 'received');

                if ($type === 'sent') {
                    // Tickets created by owner (for Admin)
                    $query->where('reporter_id', $owner->id);
                } else {
                    // Tickets received by owner (from Tenants)
                    $query->where(function ($q) use ($owner) {
                        $q->where('assignee_id', $owner->id)
                            ->orWhere(function ($subQuery) use ($owner) {
                                $subQuery->where('related_type', Property::class)
                                    ->whereHasMorph(
                                        'related',
                                        [Property::class],
                                        fn($morphQuery) => $morphQuery->where('owner_id', $owner->id)
                                    );
                            });
                    });
                }
            })
            ->when($request->status, function ($query, $status) {
                if ($status !== 'all') {
                    $query->where('status', $status);
                }
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                        ->orWhere('ticket_code', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => OwnerTicketResource::collection($tickets),
        ]);
    }
}
