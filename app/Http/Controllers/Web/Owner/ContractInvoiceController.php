<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContractInvoiceController extends Controller
{
    /**
     * List invoices for a contract belonging to the owner.
     */
    public function __invoke(Request $request, Contract $contract): JsonResponse
    {
        $ownerId = $request->user()->id;
        $this->authorizeContract($contract, $ownerId);

        return response()->json([
            'data' => $contract->invoices()
                ->with(['payments' => fn ($query) => $query->latest()])
                ->orderByDesc('due_date')
                ->get(),
        ]);
    }

    private function authorizeContract(Contract $contract, int $ownerId): void
    {
        $propertyOwnerId = optional(optional(optional($contract->room)->roomType)->property)->owner_id;
        abort_if($propertyOwnerId !== $ownerId, 403, 'Kontrak tidak ditemukan untuk akun ini.');
    }
}
