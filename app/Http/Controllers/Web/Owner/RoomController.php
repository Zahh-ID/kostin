<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $ownerId = $request->user()->id;

        /** @var LengthAwarePaginator $rooms */
        $rooms = Room::query()
            ->whereHas('roomType.property', fn ($query) => $query->where('owner_id', $ownerId))
            ->with(['roomType.property'])
            ->orderBy('room_code')
            ->paginate(15)
            ->withQueryString();

        return view('owner.rooms.index', [
            'rooms' => $rooms,
        ]);
    }

    public function show(Request $request, Room $room): View
    {
        $this->ensureOwnerOwnsRoom($request->user()->id, $room);

        $room->load([
            'roomType.property',
            'contracts' => fn ($query) => $query->orderByDesc('start_date')->limit(3),
        ]);

        return view('owner.rooms.show', [
            'room' => $room,
        ]);
    }

    public function create(Request $request): View
    {
        $ownerId = $request->user()->id;

        $properties = Property::query()
            ->where('owner_id', $ownerId)
            ->with('roomTypes:id,property_id,name')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('owner.rooms.create', [
            'properties' => $properties,
        ]);
    }

    public function edit(Request $request, Room $room): View
    {
        $this->ensureOwnerOwnsRoom($request->user()->id, $room);

        $ownerId = $request->user()->id;
        $room->load(['roomType.property']);

        $roomTypes = RoomType::query()
            ->whereHas('property', fn ($query) => $query->where('owner_id', $ownerId))
            ->with('property:id,name')
            ->orderBy('name')
            ->get();

        return view('owner.rooms.edit', [
            'room' => $room,
            'roomTypes' => $roomTypes,
        ]);
    }

    private function ensureOwnerOwnsRoom(int $ownerId, Room $room): void
    {
        abort_if(optional(optional($room->roomType)->property)->owner_id !== $ownerId, 403, 'Kamar tidak ditemukan.');
    }
}
