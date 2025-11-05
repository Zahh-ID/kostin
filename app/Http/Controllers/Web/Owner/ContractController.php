<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(Request $request): View
    {
        $ownerId = $request->user()->id;

        /** @var LengthAwarePaginator $contracts */
        $contracts = Contract::query()
            ->whereHas('room.roomType.property', fn ($query) => $query->where('owner_id', $ownerId))
            ->with([
                'tenant:id,name,email,phone',
                'room.roomType.property',
                'invoices' => fn ($query) => $query->latest('due_date')->limit(1),
            ])
            ->latest('start_date')
            ->paginate(15)
            ->withQueryString();

        return view('owner.contracts.index', [
            'contracts' => $contracts,
        ]);
    }

    public function show(Request $request, Contract $contract): View
    {
        $this->ensureOwnerOwnsContract($request->user()->id, $contract);

        $contract->load([
            'tenant:id,name,email,phone',
            'room.roomType.property',
            'invoices' => fn ($query) => $query->orderByDesc('due_date'),
        ]);

        return view('owner.contracts.show', [
            'contract' => $contract,
        ]);
    }

    private function ensureOwnerOwnsContract(int $ownerId, Contract $contract): void
    {
        $ownerOfContract = optional(optional(optional($contract->room)->roomType)->property)->owner_id;
        abort_if($ownerOfContract !== $ownerId, 403, 'Kontrak tidak ditemukan.');
    }
}
