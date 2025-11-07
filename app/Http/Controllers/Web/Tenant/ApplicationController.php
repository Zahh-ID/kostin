<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ApplicationStoreRequest;
use App\Models\Property;
use App\Models\RentalApplication;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        /** @var LengthAwarePaginator $applications */
        $applications = RentalApplication::query()
            ->where('tenant_id', $request->user()->id)
            ->with(['property', 'roomType'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('tenant.applications.index', [
            'applications' => $applications,
        ]);
    }

    public function create(Request $request): View
    {
        $properties = Property::query()
            ->where('status', 'approved')
            ->with('roomTypes.rooms')
            ->orderBy('name')
            ->get();

        $selectedPropertyId = $request->integer('property_id');
        $selectedProperty = $properties->firstWhere('id', $selectedPropertyId);

        return view('tenant.applications.create', [
            'properties' => $properties,
            'selectedProperty' => $selectedPropertyId,
            'selectedPropertyModel' => $selectedProperty,
        ]);
    }

    public function store(ApplicationStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $property = Property::where('status', 'approved')->findOrFail($validated['property_id']);

        abort_unless(
            $property->roomTypes()->where('id', $validated['room_type_id'])->exists(),
            422,
            __('Room type tidak valid untuk properti ini.')
        );

        unset($validated['terms_agreed']);

        $application = RentalApplication::create([
            ...$validated,
            'tenant_id' => $request->user()->id,
            'status' => 'pending',
            'terms_text' => $property->rules_text,
            'terms_accepted_at' => now(),
        ]);

        return redirect()
            ->route('tenant.applications.show', $application)
            ->with('status', __('Pengajuan kontrak berhasil dikirim. Pemilik akan segera meninjau.'));
    }

    public function show(Request $request, RentalApplication $application): View
    {
        abort_if($application->tenant_id !== $request->user()->id, 403);

        $application->load(['property', 'roomType', 'room.roomType']);

        return view('tenant.applications.show', [
            'application' => $application,
        ]);
    }
}
