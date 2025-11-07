<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractTerminationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContractTerminationController extends Controller
{
    public function store(Request $request, Contract $contract): RedirectResponse
    {
        $tenant = $request->user();
        abort_if($contract->tenant_id !== $tenant->id, 403);

        $validated = $request->validate([
            'requested_end_date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $hasPending = $contract->terminationRequests()
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('status', __('Permintaan pemutusan kontrak sebelumnya masih menunggu persetujuan.'));
        }

        ContractTerminationRequest::create([
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'requested_end_date' => $validated['requested_end_date'],
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('status', __('Permintaan pengakhiran kontrak telah dikirim ke pemilik.'));
    }
}
