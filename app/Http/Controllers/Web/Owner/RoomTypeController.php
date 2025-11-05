<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\RoomType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomTypeController extends Controller
{
    public function index(Request $request): View
    {
        $ownerId = $request->user()->id;

        /** @var LengthAwarePaginator $roomTypes */
        $roomTypes = RoomType::query()
            ->whereHas('property', fn ($query) => $query->where('owner_id', $ownerId))
            ->with(['property', 'rooms'])
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('owner.room-types.index', [
            'roomTypes' => $roomTypes,
        ]);
    }

    public function show(Request $request, RoomType $roomType): View
    {
        $this->ensureOwnerOwnsRoomType($request->user()->id, $roomType);

        $roomType->load(['property', 'rooms.contracts' => fn ($query) => $query->latest()->limit(1)]);

        return view('owner.room-types.show', [
            'roomType' => $roomType,
        ]);
    }

    public function create(Request $request): View
    {
        $properties = Property::query()
            ->where('owner_id', $request->user()->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('owner.room-types.create', [
            'properties' => $properties,
        ]);
    }

    public function edit(Request $request, RoomType $roomType): View
    {
        $this->ensureOwnerOwnsRoomType($request->user()->id, $roomType);

        $roomType->load(['property', 'rooms']);

        $properties = Property::query()
            ->where('owner_id', $request->user()->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('owner.room-types.edit', [
            'roomType' => $roomType,
            'properties' => $properties,
        ]);
    }

    private function ensureOwnerOwnsRoomType(int $ownerId, RoomType $roomType): void
    {
        abort_if(optional($roomType->property)->owner_id !== $ownerId, 403, 'Tipe kamar tidak ditemukan.');
    }
}
