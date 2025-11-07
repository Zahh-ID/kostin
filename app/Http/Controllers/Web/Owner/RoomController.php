<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\RoomStoreRequest;
use App\Http\Requests\Owner\RoomUpdateRequest;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request, ?RoomType $roomType = null): View|RedirectResponse
    {
        $ownerId = $request->user()->id;

        if (! $roomType && ! $request->filled('property_id')) {
            return redirect()
                ->route('owner.properties.index')
                ->with('error', __('Silakan pilih properti terlebih dahulu untuk mengelola kamar.'));
        }

        $contextProperty = $this->resolveContextProperty($request, $ownerId, $roomType);
        $stats = Room::query()
            ->selectRaw("COUNT(*) as total_rooms, SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_rooms")
            ->whereHas('roomType', fn ($query) => $query->where('property_id', $contextProperty->id))
            ->first();

        $contextProperty->total_rooms = (int) ($stats->total_rooms ?? 0);
        $contextProperty->available_rooms = (int) ($stats->available_rooms ?? 0);

        $roomsQuery = Room::query()
            ->whereHas('roomType.property', fn ($query) => $query->where('owner_id', $ownerId))
            ->with(['roomType.property'])
            ->orderBy('room_code');

        if ($roomType) {
            $roomsQuery->where('room_type_id', $roomType->getKey());
        } else {
            $roomsQuery->whereHas('roomType', fn ($query) => $query->where('property_id', $contextProperty->id));
        }

        $rooms = $roomsQuery
            ->paginate(15)
            ->withQueryString();

        return view('owner.rooms.index', [
            'rooms' => $rooms,
            'filteredRoomType' => $roomType,
            'contextProperty' => $contextProperty,
            'propertyId' => $contextProperty->id,
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
            'contextProperty' => $room->roomType?->property,
        ]);
    }

    public function create(Request $request, ?RoomType $roomType = null): View|RedirectResponse
    {
        $ownerId = $request->user()->id;

        if (! $roomType && ! $request->filled('property_id')) {
            return redirect()
                ->route('owner.properties.index')
                ->with('error', __('Silakan pilih properti terlebih dahulu untuk menambah kamar.'));
        }

        $selectedProperty = $this->resolveContextProperty($request, $ownerId, $roomType);
        $selectedProperty->loadMissing(['roomTypes' => fn ($query) => $query->orderBy('name')]);
        $defaultRoomType = $this->ensureDefaultRoomType($selectedProperty, $roomType);

        return view('owner.rooms.create', [
            'selectedProperty' => $selectedProperty,
            'defaultRoomType' => $defaultRoomType,
        ]);
    }

    public function store(RoomStoreRequest $request, ?RoomType $roomType = null): RedirectResponse
    {
        $ownerId = $request->user()->id;
        $data = $request->validated();

        $roomType = $this->resolveRoomTypeForOwner(
            ownerId: $ownerId,
            roomType: $roomType,
            roomTypeId: (int) $data['room_type_id']
        );

        $room = $roomType->rooms()->create([
            'room_code' => $data['room_code'],
            'status' => $data['status'],
            'custom_price' => $data['custom_price'] ?? null,
            'facilities_override_json' => $this->normalizeFacilities($data['facilities_override'] ?? null),
            'description' => $data['description'],
            'photos_json' => $this->storeUploadedPhotos($request->file('photos', []) ?? []),
        ]);

        return redirect()
            ->route('owner.rooms.show', [
                'room' => $room,
                'property_id' => optional($roomType->property)->id,
            ])
            ->with('status', __('Kamar berhasil ditambahkan.'));
    }

    public function edit(Request $request, Room $room): View
    {
        $this->ensureOwnerOwnsRoom($request->user()->id, $room);

        $ownerId = $request->user()->id;
        $room->load([
            'roomType.property',
            'contracts' => fn ($query) => $query->orderByDesc('start_date')->limit(3),
        ]);

        return view('owner.rooms.edit', [
            'room' => $room,
            'contextProperty' => $room->roomType?->property,
        ]);
    }

    public function update(RoomUpdateRequest $request, Room $room): RedirectResponse
    {
        $this->ensureOwnerOwnsRoom($request->user()->id, $room);

        $data = $request->validated();

        $room->update([
            'status' => $data['status'],
            'custom_price' => $data['custom_price'] ?? null,
            'facilities_override_json' => $this->normalizeFacilities($data['facilities_override'] ?? null),
            'description' => $data['description'],
            'photos_json' => $this->syncRoomPhotos($room, $request),
        ]);

        return redirect()
            ->route('owner.rooms.show', [
                'room' => $room,
                'property_id' => optional($room->roomType)->property_id,
            ])
            ->with('status', __('Kamar berhasil diperbarui.'));
    }

    public function destroy(Request $request, Room $room): RedirectResponse
    {
        $this->ensureOwnerOwnsRoom($request->user()->id, $room);

        $hasActiveContract = $room->contracts()
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($hasActiveContract) {
            return back()
                ->withErrors(['room' => __('Kamar tidak dapat dihapus selama masih memiliki kontrak aktif.')]);
        }

        $this->deleteRoomPhotos($room);
        $room->delete();

        return redirect()
            ->route('owner.rooms.index', [
                'property_id' => optional($room->roomType)->property_id,
            ])
            ->with('status', __('Kamar dihapus.'));
    }

    private function ensureOwnerOwnsRoom(int $ownerId, Room $room): void
    {
        abort_if(optional(optional($room->roomType)->property)->owner_id !== $ownerId, 403, 'Kamar tidak ditemukan.');
    }

    private function resolveRoomTypeForOwner(int $ownerId, ?RoomType $roomType, int $roomTypeId): RoomType
    {
        if ($roomType && optional($roomType->property)->owner_id === $ownerId) {
            return $roomType;
        }

        return RoomType::query()
            ->whereKey($roomTypeId)
            ->whereHas('property', fn ($query) => $query->where('owner_id', $ownerId))
            ->firstOrFail();
    }

    private function resolveContextProperty(Request $request, int $ownerId, ?RoomType $roomType = null): Property
    {
        if ($roomType) {
            $roomType->loadMissing('property');
            $property = $roomType->property;
            abort_if(! $property || $property->owner_id !== $ownerId, 403, 'Tipe kamar tidak ditemukan.');

            return $property;
        }

        $propertyId = $request->integer('property_id');

        return Property::query()
            ->where('owner_id', $ownerId)
            ->findOrFail($propertyId, ['id', 'name', 'address']);
    }

    private function normalizeFacilities(?array $facilities): ?array
    {
        if (! $facilities) {
            return null;
        }

        $items = collect($facilities)
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return empty($items) ? null : $items;
    }

    private function ensureDefaultRoomType(Property $property, ?RoomType $preferred = null): RoomType
    {
        if ($preferred && $preferred->property_id === $property->id) {
            return $preferred;
        }

        $existing = $property->roomTypes->first() ?? $property->roomTypes()->orderBy('id')->first();
        if ($existing) {
            return $existing;
        }

        return $property->roomTypes()->create([
            'name' => 'Default',
            'area_m2' => null,
            'bathroom_type' => null,
            'base_price' => 0,
            'deposit' => 0,
            'facilities_json' => [],
        ]);
    }

    /**
     * @param \Illuminate\Http\UploadedFile[] $files
     */
    private function storeUploadedPhotos(array $files): array
    {
        return collect($files)
            ->filter()
            ->map(fn ($file) => $file->store('rooms', 'public'))
            ->values()
            ->all();
    }

    private function syncRoomPhotos(Room $room, Request $request): array
    {
        $existing = collect($room->photos_json ?? []);
        $remove = collect($request->input('remove_photos', []));

        $remaining = $existing->reject(fn ($path) => $remove->contains($path))->values();

        $newPhotos = $request->file('photos', []) ?? [];
        if (! empty($newPhotos)) {
            $remaining = $remaining->merge($this->storeUploadedPhotos($newPhotos));
        }

        if ($remaining->isEmpty()) {
            throw ValidationException::withMessages([
                'photos' => __('Minimal satu foto kamar diperlukan.'),
            ]);
        }

        $remove->each(fn ($path) => $this->deletePhotoPath($path));

        return $remaining->values()->all();
    }

    private function deleteRoomPhotos(Room $room): void
    {
        collect($room->photos_json ?? [])->each(fn ($path) => $this->deletePhotoPath($path));
    }

    private function deletePhotoPath(?string $path): void
    {
        if (! $path || Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
