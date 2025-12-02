<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PropertyRoomController extends Controller
{
    public function index(Property $property)
    {
        // Ensure user owns the property
        if ($property->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $rooms = $property->rooms()
            ->with('roomType')
            ->orderBy('room_code')
            ->get();

        return response()->json([
            'data' => $rooms
        ]);
    }

    public function storeBulk(Request $request, Property $property)
    {
        if ($property->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'prefix' => 'nullable|string|max:10',
            'start_number' => 'required|integer|min:0',
            'count' => 'required|integer|min:1|max:50',
            'suffix' => 'nullable|string|max:10',
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        // Verify room type belongs to property
        if ($property->roomTypes()->where('id', $validated['room_type_id'])->doesntExist()) {
            throw ValidationException::withMessages([
                'room_type_id' => 'Tipe kamar tidak valid untuk properti ini.'
            ]);
        }

        $rooms = [];
        $prefix = $validated['prefix'] ?? '';
        $suffix = $validated['suffix'] ?? '';
        $start = $validated['start_number'];
        $count = $validated['count'];

        DB::transaction(function () use ($validated, $prefix, $suffix, $start, $count, &$rooms, $property) {
            for ($i = 0; $i < $count; $i++) {
                $number = $start + $i;
                $code = $prefix . $number . $suffix;

                // Check if room code already exists for this property
                // Assuming room_code is unique per property (or globally? usually per property)
                // But Room model doesn't have property_id, it has room_type_id.
                // So we check if any room in this property has this code.

                $exists = Room::whereHas('roomType', function ($query) use ($property) {
                    $query->where('property_id', $property->id);
                })->where('room_code', $code)->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'start_number' => "Kamar dengan kode '$code' sudah ada."
                    ]);
                }

                $rooms[] = Room::create([
                    'room_type_id' => $validated['room_type_id'],
                    'room_code' => $code,
                    'status' => $validated['status'],
                    'custom_price' => null,
                ]);
            }
        });

        return response()->json([
            'message' => count($rooms) . ' kamar berhasil dibuat.',
            'data' => $rooms
        ]);
    }
}
