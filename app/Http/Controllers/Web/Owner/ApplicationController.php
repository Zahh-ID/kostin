<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\RentalApplication;
use App\Models\Room;
use App\Services\ContractBillingService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly ContractBillingService $contractBillingService,
    ) {
    }

    public function index(Request $request): View
    {
        $ownerId = $request->user()->id;

        /** @var LengthAwarePaginator $applications */
        $applications = RentalApplication::query()
            ->whereHas('property', fn ($query) => $query->where('owner_id', $ownerId))
            ->with(['tenant', 'property', 'roomType'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('owner.applications.index', [
            'applications' => $applications,
        ]);
    }

    public function show(Request $request, RentalApplication $application): View
    {
        $this->authorizeOwner($request->user()->id, $application);

        $application->load([
            'tenant',
            'property.roomTypes.rooms',
            'roomType',
            'room',
        ]);

        $roomTypeIds = $application->property->roomTypes->pluck('id');
        $availableRooms = Room::query()
            ->whereIn('room_type_id', $roomTypeIds)
            ->whereHas('roomType.property', fn ($query) => $query->where('owner_id', $request->user()->id))
            ->get();

        return view('owner.applications.show', [
            'application' => $application,
            'availableRooms' => $availableRooms,
        ]);
    }

    public function update(Request $request, RentalApplication $application): RedirectResponse
    {
        $this->authorizeOwner($request->user()->id, $application);

        $action = $request->input('action');

        if ($action === 'approve') {
            $data = $request->validate([
                'room_id' => ['required', 'exists:rooms,id'],
                'start_date' => ['required', 'date'],
                'duration_months' => ['required', 'integer', 'min:1', 'max:36'],
                'price_per_month' => ['required', 'numeric', 'min:0'],
                'billing_day' => ['required', 'integer', 'between:1,28'],
                'owner_notes' => ['nullable', 'string', 'max:2000'],
            ]);

            $room = Room::where('id', $data['room_id'])
                ->whereHas('roomType.property', fn ($query) => $query->where('owner_id', $request->user()->id))
                ->firstOrFail();

            $endDate = \Illuminate\Support\Carbon::parse($data['start_date'])
                ->copy()
                ->addMonthsNoOverflow((int) $data['duration_months'])
                ->subDay();

            $contract = Contract::create([
                'tenant_id' => $application->tenant_id,
                'room_id' => $room->id,
                'start_date' => $data['start_date'],
                'end_date' => $endDate,
                'price_per_month' => $data['price_per_month'],
                'billing_day' => $data['billing_day'],
                'deposit_amount' => 0,
                'grace_days' => 0,
                'late_fee_per_day' => 0,
                'status' => 'active',
            ]);

            $application->update([
                'status' => 'approved',
                'room_id' => $room->id,
                'owner_notes' => $data['owner_notes'] ?? null,
                'approved_at' => now(),
            ]);

            $this->contractBillingService->ensureInitialInvoice($contract);

            return redirect()
                ->route('owner.applications.show', $application)
                ->with('status', __('Pengajuan disetujui dan kontrak telah dibuat.'));
        }

        if ($action === 'reject') {
            $data = $request->validate([
                'owner_notes' => ['required', 'string', 'max:2000'],
            ]);

            $application->update([
                'status' => 'rejected',
                'owner_notes' => $data['owner_notes'],
                'rejected_at' => now(),
            ]);

            return redirect()
                ->route('owner.applications.show', $application)
                ->with('status', __('Pengajuan ditolak.'));
        }

        return redirect()
            ->route('owner.applications.show', $application)
            ->with('status', __('Tidak ada aksi yang dilakukan.'));
    }

    private function authorizeOwner(int $ownerId, RentalApplication $application): void
    {
        abort_if($application->property?->owner_id !== $ownerId, 403);
    }
}
