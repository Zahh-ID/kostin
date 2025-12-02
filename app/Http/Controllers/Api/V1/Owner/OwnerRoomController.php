<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\Owner\OwnerRoomResource;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OwnerRoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::query()
            ->with(['roomType.property'])
            ->whereHas('roomType.property', fn($query) => $query->where('owner_id', $request->user()->id))
            ->latest()
            ->get();

        return OwnerRoomResource::collection($rooms);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'room_code' => ['required', 'string', 'max:50'],
            'custom_price' => ['nullable', 'numeric'],
            'status' => ['required', 'string', 'in:available,occupied,maintenance'],
            'description' => ['nullable', 'string'],
        ]);

        $roomType = RoomType::query()
            ->where('id', $validated['room_type_id'])
            ->whereHas('property', fn($query) => $query->where('owner_id', $request->user()->id))
            ->firstOrFail();

        $room = $roomType->rooms()->create($validated);

        return (new OwnerRoomResource($room->load('roomType.property')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Room $room)
    {
        $this->authorize('view', $room->roomType->property);
        return new OwnerRoomResource($room->load('roomType.property'));
    }

    public function update(Request $request, Room $room)
    {
        $this->authorize('update', $room->roomType->property);

        $validated = $request->validate([
            'room_code' => ['sometimes', 'string', 'max:50'],
            'custom_price' => ['nullable', 'numeric'],
            'status' => ['sometimes', 'string', 'in:available,occupied,maintenance'],
            'description' => ['nullable', 'string'],
        ]);

        $room->update($validated);

        return new OwnerRoomResource($room->load('roomType.property'));
    }

    public function destroy(Room $room)
    {
        $this->authorize('delete', $room->roomType->property);
        $room->delete();
        return response()->noContent();
    }
}
