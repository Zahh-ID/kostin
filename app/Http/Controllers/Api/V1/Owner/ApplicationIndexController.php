<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\Owner\OwnerApplicationResource;
use App\Models\RentalApplication;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationIndexController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $owner */
        $owner = $request->user();

        $applications = RentalApplication::query()
            ->with(['tenant', 'property', 'roomType', 'room'])
            ->whereHas('property', fn ($query) => $query->where('owner_id', $owner->id))
            ->latest()
            ->limit(30)
            ->get();

        return response()->json([
            'data' => OwnerApplicationResource::collection($applications),
        ]);
    }
}
