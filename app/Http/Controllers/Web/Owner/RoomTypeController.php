<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\RoomType;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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

        $selectedPropertyId = $request->integer('property_id');
        $selectedProperty = $selectedPropertyId
            ? $properties->firstWhere('id', $selectedPropertyId)
            : null;

        return view('owner.room-types.create', [
            'properties' => $properties,
            'selectedPropertyId' => $selectedPropertyId ?: optional($properties->first())->id,
            'selectedProperty' => $selectedProperty,
        ]);
    }

    public function store(Request $request, Property $property): RedirectResponse
    {
        $this->ensureOwnerOwnsProperty($request->user()->id, $property);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'area_m2' => ['nullable', 'numeric', 'min:0'],
            'bathroom_type' => ['required', 'in:inside,outside'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'deposit' => ['nullable', 'numeric', 'min:0'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['nullable', 'string', 'max:100'],
        ]);

        $property->roomTypes()->create([
            'name' => $validated['name'],
            'area_m2' => $validated['area_m2'] ?? null,
            'bathroom_type' => $validated['bathroom_type'] ?? null,
            'base_price' => $validated['base_price'],
            'deposit' => $validated['deposit'] ?? null,
            'facilities_json' => array_filter($validated['facilities'] ?? []),
        ]);

        return redirect()
            ->route('owner.properties.show', $property)
            ->with('status', __('Tipe kamar berhasil ditambahkan.'));
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

    private function ensureOwnerOwnsProperty(int $ownerId, Property $property): void
    {
        abort_if($property->owner_id !== $ownerId, 403, 'Properti tidak ditemukan.');
    }
}
