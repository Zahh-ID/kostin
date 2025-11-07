<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function index(Request $request): View
    {
        /** @var LengthAwarePaginator $properties */
        $properties = Property::query()
            ->where('status', 'pending')
            ->with('owner:id,name,email')
            ->orderBy('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.moderations.index', [
            'properties' => $properties,
        ]);
    }

    public function show(Property $property): View
    {
        abort_unless($property->status === 'pending', 404);

        $property->load([
            'owner:id,name,email,phone',
            'roomTypes.rooms',
        ]);

        return view('admin.moderations.show', [
            'property' => $property,
        ]);
    }

    public function approve(Request $request, Property $property): RedirectResponse
    {
        if ($property->status !== 'pending') {
            return back()->with('status', __('Properti tidak berada dalam antrian moderasi.'));
        }

        $validated = $request->validate([
            'moderation_notes' => ['nullable', 'string'],
        ]);

        $property->update([
            'status' => 'approved',
            'moderation_notes' => $validated['moderation_notes'] ?? null,
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        $this->recordAudit('property.moderation.approved', 'property', $property->id, $validated);

        return redirect()
            ->route('admin.moderations.index')
            ->with('status', "Properti {$property->name} telah disetujui.");
    }

    public function reject(Request $request, Property $property): RedirectResponse
    {
        if ($property->status !== 'pending') {
            return back()->with('status', __('Properti tidak berada dalam antrian moderasi.'));
        }

        $validated = $request->validate([
            'moderation_notes' => ['required', 'string'],
        ]);

        $property->update([
            'status' => 'rejected',
            'moderation_notes' => $validated['moderation_notes'],
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        $this->recordAudit('property.moderation.rejected', 'property', $property->id, $validated);

        return redirect()
            ->route('admin.moderations.index')
            ->with('status', "Properti {$property->name} ditandai sebagai ditolak.");
    }
}
