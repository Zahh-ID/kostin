<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\OwnerPropertyStoreRequest;
use App\Http\Requests\Owner\OwnerPropertyUpdateRequest;
use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $owner = $request->user();

        /** @var LengthAwarePaginator $properties */
        $properties = $owner->properties()
            ->with([
                'roomTypes.rooms',
                'sharedTasks' => fn ($query) => $query->latest()->limit(2),
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('owner.properties.index', [
            'properties' => $properties,
        ]);
    }

    public function show(Request $request, Property $property): View
    {
        $this->authorize('view', $property);

        $property->load([
            'roomTypes.rooms.contracts' => fn ($query) => $query->latest('start_date')->limit(1),
            'sharedTasks.logs' => fn ($query) => $query->latest()->limit(3),
            'moderator:id,name',
        ]);

        return view('owner.properties.show', [
            'property' => $property,
        ]);
    }

    public function create(Request $request): View
    {
        return view('owner.properties.create');
    }

    public function edit(Request $request, Property $property): View
    {
        $this->authorize('update', $property);

        $property->load(['roomTypes.rooms']);

        return view('owner.properties.edit', [
            'property' => $property,
        ]);
    }

    public function store(OwnerPropertyStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Property::class);

        $payload = $request->validated();

        $property = $request->user()->properties()->create([
            ...$payload,
            'status' => 'draft',
        ]);

        $this->recordAudit('property.create', 'property', $property->id, ['status' => 'draft']);

        return redirect()
            ->route('owner.properties.show', $property)
            ->with('status', __('Properti berhasil disimpan sebagai draft. Ajukan moderasi ketika siap dipublikasikan.'));
    }

    public function update(OwnerPropertyUpdateRequest $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        $payload = $request->validated();

        $property->update($payload);

        $this->recordAudit('property.update', 'property', $property->id, ['status' => $property->status]);

        return redirect()
            ->route('owner.properties.show', $property)
            ->with('status', __('Data properti diperbarui.'));
    }

    public function submit(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        if (! in_array($property->status, ['draft', 'rejected'], true)) {
            return back()->with('status', __('Properti tidak dapat diajukan karena sedang menunggu atau sudah disetujui.'));
        }

        $previousStatus = $property->status;

        $property->update([
            'status' => 'pending',
            'moderation_notes' => null,
            'moderated_by' => null,
            'moderated_at' => null,
        ]);

        $this->recordAudit('property.submit', 'property', $property->id, [
            'from' => $previousStatus,
            'to' => 'pending',
        ]);

        return back()->with('status', __('Properti dikirim untuk moderasi admin. Kami akan memberi tahu setelah diproses.'));
    }

    public function withdraw(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        if (! in_array($property->status, ['pending', 'approved'], true)) {
            return back()->with('status', __('Status properti saat ini tidak dapat ditarik.'));
        }

        $previousStatus = $property->status;

        $property->update([
            'status' => 'draft',
            'moderation_notes' => $previousStatus === 'approved'
                ? $property->moderation_notes
                : null,
            'moderated_by' => $previousStatus === 'approved'
                ? $property->moderated_by
                : null,
            'moderated_at' => $previousStatus === 'approved'
                ? $property->moderated_at
                : null,
        ]);

        $this->recordAudit('property.withdraw', 'property', $property->id, ['from' => $previousStatus]);

        $message = $previousStatus === 'approved'
            ? __('Properti berhasil diarsipkan. Ajukan kembali jika ingin dipublikasikan.')
            : __('Pengajuan moderasi dibatalkan. Silakan revisi data sebelum mengajukan ulang.');

        return back()->with('status', $message);
    }
}
