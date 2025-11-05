<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeActionController extends Controller
{
    /**
     * Store a new room type under the specified property.
     */
    public function store(Request $request, Property $property)
    {
        $owner = $request->user();
        $this->authorizeProperty($property, $owner->id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'area_m2' => ['nullable', 'numeric', 'min:0'],
            'bathroom_type' => ['nullable', 'string', 'max:50'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'deposit' => ['nullable', 'numeric', 'min:0'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['nullable', 'string', 'max:255'],
        ]);

        $roomType = RoomType::create([
            'property_id' => $property->id,
            'name' => $validated['name'],
            'area_m2' => $validated['area_m2'] ?? null,
            'bathroom_type' => $validated['bathroom_type'] ?? null,
            'base_price' => $validated['base_price'],
            'deposit' => $validated['deposit'] ?? 0,
            'facilities_json' => $validated['facilities'] ?? [],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Tipe kamar berhasil dibuat.',
                'data' => $roomType,
            ], 201);
        }

        return redirect()
            ->route('owner.room-types.show', $roomType)
            ->with('status', 'Tipe kamar berhasil dibuat.');
    }

    private function authorizeProperty(Property $property, int $ownerId): void
    {
        abort_if($property->owner_id !== $ownerId, 403, 'Properti tidak ditemukan untuk akun ini.');
    }
}
