<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\Owner\OwnerPropertyResource;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyIndexController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $owner */
        $owner = $request->user();

        $properties = Property::query()
            ->withCount('roomTypes')
            ->where('owner_id', $owner->id)
            ->latest()
            ->get();

        $statusCounts = Property::query()
            ->selectRaw('status, count(*) as aggregate')
            ->where('owner_id', $owner->id)
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return response()->json([
            'data' => OwnerPropertyResource::collection($properties),
            'meta' => [
                'counts' => [
                    'approved' => (int) ($statusCounts['approved'] ?? 0),
                    'pending' => (int) ($statusCounts['pending'] ?? 0),
                    'draft' => (int) ($statusCounts['draft'] ?? 0),
                    'rejected' => (int) ($statusCounts['rejected'] ?? 0),
                ],
            ],
        ]);
    }
}
