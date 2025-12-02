<?php

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyController extends Controller
{
    public function show(Property $property): JsonResource
    {
        abort_unless($property->status === 'approved', 404);

        $property->load(['owner', 'roomTypes.rooms.roomType']);

        return new PropertyResource($property);
    }
}
