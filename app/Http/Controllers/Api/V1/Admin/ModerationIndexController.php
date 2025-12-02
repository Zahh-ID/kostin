<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminPropertyModerationResource;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModerationIndexController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $properties = Property::query()
            ->with('owner:id,name,email')
            ->whereIn('status', ['pending', 'rejected'])
            ->latest()
            ->limit(50)
            ->get();

        return response()->json([
            'data' => AdminPropertyModerationResource::collection($properties),
        ]);
    }
}
