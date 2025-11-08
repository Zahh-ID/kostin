<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\ContractTerminationRequest;
use App\Services\ContractBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractTerminationController extends Controller
{
    public function __construct(
        private readonly ContractBillingService $contractBillingService,
    ) {
    }

    public function index(Request $request): View
    {
        $ownerId = $request->user()->id;

        $requests = ContractTerminationRequest::query()
            ->whereHas('contract.room.roomType.property', fn ($query) => $query->where('owner_id', $ownerId))
            ->with(['contract.room.roomType.property', 'tenant'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('owner.contracts.termination-index', [
            'requests' => $requests,
        ]);
    }

    public function update(Request $request, ContractTerminationRequest $terminationRequest): RedirectResponse
    {
        $ownerId = $request->user()->id;
        abort_unless(optional(optional(optional($terminationRequest->contract)->room)->roomType)->property?->owner_id === $ownerId, 403);

        $data = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'owner_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($terminationRequest->status !== 'pending') {
            return back()->with('status', __('Permintaan ini sudah diproses.'));
        }

        if ($data['action'] === 'approve') {
            $contract = $terminationRequest->contract;

            if ($contract) {
                $contract->update([
                    'status' => 'ended',
                    'end_date' => $terminationRequest->requested_end_date,
                ]);

                $this->contractBillingService->cancelOutstandingInvoices($contract);
            }

            $terminationRequest->status = 'approved';
        } else {
            $terminationRequest->status = 'rejected';
        }

        $terminationRequest->owner_notes = $data['owner_notes'] ?? null;
        $terminationRequest->resolved_at = now();
        $terminationRequest->save();

        $message = $data['action'] === 'approve'
            ? __('Permintaan pengakhiran disetujui.')
            : __('Permintaan pengakhiran ditolak.');

        return back()->with('status', $message);
    }
}
