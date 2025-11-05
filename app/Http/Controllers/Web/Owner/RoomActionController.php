<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomActionController extends Controller
{
    /**
     * Store a room for the given room type.
     */
    public function store(Request $request, RoomType $roomType)
    {
        $owner = $request->user();
        $this->authorizeRoomType($roomType, $owner->id);

        $validated = $request->validate([
            'room_code' => ['required', 'string', 'max:50', Rule::unique('rooms', 'room_code')],
            'status' => ['required', Rule::in(['available', 'occupied', 'maintenance'])],
            'custom_price' => ['nullable', 'numeric', 'min:0'],
            'facilities_override' => ['nullable', 'array'],
            'facilities_override.*' => ['nullable', 'string', 'max:255'],
        ]);

        $room = Room::create([
            'room_type_id' => $roomType->id,
            'room_code' => $validated['room_code'],
            'status' => $validated['status'],
            'custom_price' => $validated['custom_price'] ?? null,
            'facilities_override_json' => $validated['facilities_override'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Kamar berhasil dibuat.',
                'data' => $room,
            ], 201);
        }

        return redirect()
            ->route('owner.rooms.show', $room)
            ->with('status', 'Kamar berhasil dibuat.');
    }

    private function authorizeRoomType(RoomType $roomType, int $ownerId): void
    {
        $propertyOwnerId = optional($roomType->property)->owner_id;
        abort_if($propertyOwnerId !== $ownerId, 403, 'Tipe kamar tidak ditemukan untuk akun ini.');
    }
}
