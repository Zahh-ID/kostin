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
            ->where(function ($query) use ($owner) {
                $query->where('reporter_id', $owner->id)
                    ->orWhere('assignee_id', $owner->id)
                    ->orWhere(function ($subQuery) use ($owner) {
                        $subQuery->where('related_type', Property::class)
                            ->whereHasMorph(
                                'related',
                                [Property::class],
                                fn ($morphQuery) => $morphQuery->where('owner_id', $owner->id)
                            );
                    });
            })
            ->latest()
            ->limit(30)
            ->get();

        return response()->json([
            'data' => OwnerTicketResource::collection($tickets),
        ]);
    }
}
