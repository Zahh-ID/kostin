<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $tenant */
        $tenant = $request->user();

        /** @var LengthAwarePaginator $contracts */
        $contracts = $tenant->contracts()
            ->with([
                'room.roomType.property',
                'invoices' => fn ($query) => $query->latest('due_date')->limit(1),
            ])
            ->latest('start_date')
            ->paginate(10)
            ->withQueryString();

        return view('tenant.contracts.index', [
            'contracts' => $contracts,
        ]);
    }

    public function show(Request $request, Contract $contract): View
    {
        /** @var User $tenant */
        $tenant = $request->user();
        $this->ensureTenantOwnsContract($tenant, $contract);

        $contract->load([
            'room.roomType.property',
            'invoices' => fn ($query) => $query->orderByDesc('due_date'),
        ]);

        return view('tenant.contracts.show', [
            'contract' => $contract,
        ]);
    }

    private function ensureTenantOwnsContract(User $tenant, Contract $contract): void
    {
        abort_if($contract->tenant_id !== $tenant->id, 403, 'Kontrak tidak ditemukan untuk akun ini.');
    }
}
