<?php

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = Property::query()
            ->where('status', 'approved')
            ->with(['roomTypes.rooms', 'owner'])
            ->when($request->filled('search'), function ($builder) use ($request) {
                $term = $request->string('search');

                $builder->where('name', 'like', "%{$term}%")
                    ->orWhere('address', 'like', "%{$term}%");
            })
            ->latest()
            ->limit(12)
            ->get();

        return PropertyResource::collection($query);
    }

    public function show(Property $property): JsonResource
    {
        abort_unless($property->status === 'approved', 404);

        $property->load(['roomTypes.rooms', 'owner']);

        return new PropertyResource($property);
    }
}
