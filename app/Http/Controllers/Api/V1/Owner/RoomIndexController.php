<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\Owner\OwnerRoomResource;
use App\Http\Resources\Owner\OwnerRoomTypeResource;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoomIndexController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $owner */
        $owner = $request->user();

        $roomTypes = RoomType::query()
            ->with('property:id,name,owner_id')
            ->withCount('rooms')
            ->whereHas('property', fn ($query) => $query->where('owner_id', $owner->id))
            ->latest()
            ->get();

        $rooms = Room::query()
            ->with(['roomType.property'])
            ->whereHas('roomType.property', fn ($query) => $query->where('owner_id', $owner->id))
            ->latest()
            ->get();

        return response()->json([
            'room_types' => OwnerRoomTypeResource::collection($roomTypes),
            'rooms' => OwnerRoomResource::collection($rooms),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var User $owner */
        $owner = $request->user();

        $validated = $request->validate([
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'room_code' => ['required', 'string', 'max:50'],
            'custom_price' => ['nullable', 'numeric'],
            'status' => ['required', 'string', 'in:available,occupied,maintenance'],
            'description' => ['nullable', 'string'],
        ]);

        $roomType = RoomType::query()
            ->where('id', $validated['room_type_id'])
            ->whereHas('property', fn ($query) => $query->where('owner_id', $owner->id))
            ->firstOrFail();

        $room = $roomType->rooms()->create($validated);

        $this->recordAudit('room.create', 'room', $room->id, ['status' => $room->status]);

        return (new OwnerRoomResource($room->load('roomType.property')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
