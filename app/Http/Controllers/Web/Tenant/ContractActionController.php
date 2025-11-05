<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class ContractActionController extends Controller
{
    public function accept(Request $request, Contract $contract)
    {
        $tenant = $request->user();
        abort_if($contract->tenant_id !== $tenant->id, 403, 'Kontrak tidak ditemukan.');

        $contract->update([
            'status' => 'active',
            'start_date' => $contract->start_date ?? now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Kontrak berhasil dikonfirmasi.',
                'data' => $contract->fresh()->load(['room.roomType.property']),
            ]);
        }

        return redirect()
            ->route('tenant.contracts.show', $contract)
            ->with('status', 'Kontrak berhasil dikonfirmasi.');
    }
}
