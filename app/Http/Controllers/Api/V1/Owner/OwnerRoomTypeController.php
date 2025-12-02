<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\Owner\OwnerRoomTypeResource;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OwnerRoomTypeController extends Controller
{
    public function index(Request $request)
    {
        $roomTypes = RoomType::query()
            ->with('property')
            ->withCount('rooms')
            ->whereHas('property', fn($query) => $query->where('owner_id', $request->user()->id))
            ->latest()
            ->get();

        return OwnerRoomTypeResource::collection($roomTypes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'area_m2' => ['nullable', 'numeric'],
            'facilities' => ['nullable', 'array'],
        ]);

        // Verify property ownership
        $request->user()->properties()->findOrFail($validated['property_id']);

        $roomType = RoomType::create([
            'property_id' => $validated['property_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'base_price' => $validated['price'],
            'area_m2' => $validated['area_m2'],
            'facilities_json' => $validated['facilities'] ?? [],
        ]);

        return (new OwnerRoomTypeResource($roomType))
            ->response()
            ->setStatusCode(201);
    }

    public function show(RoomType $roomType)
    {
        $this->authorize('view', $roomType->property);
        return new OwnerRoomTypeResource($roomType->load('property'));
    }

    public function update(Request $request, RoomType $roomType)
    {
        $this->authorize('update', $roomType->property);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'area_m2' => ['nullable', 'numeric'],
            'facilities' => ['nullable', 'array'],
        ]);

        $roomType->update($validated);

        return new OwnerRoomTypeResource($roomType);
    }

    public function destroy(RoomType $roomType)
    {
        $this->authorize('delete', $roomType->property);
        $roomType->delete();
        return response()->noContent();
    }
}
