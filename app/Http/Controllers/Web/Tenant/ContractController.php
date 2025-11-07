<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
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
            'nextCoverageStart' => $this->determineNextCoverageMonth($contract),
        ]);
    }

    public function download(Request $request, Contract $contract): Response
    {
        /** @var User $tenant */
        $tenant = $request->user();
        $this->ensureTenantOwnsContract($tenant, $contract);

        $contract->load([
            'room.roomType.property.owner',
            'tenant',
            'invoices' => fn ($query) => $query->latest('due_date'),
        ]);

        $pdf = Pdf::loadView('tenant.contracts.pdf', [
            'contract' => $contract,
            'tenant' => $tenant,
            'property' => $contract->room->roomType->property,
            'owner' => optional($contract->room->roomType->property)->owner,
        ])->setPaper('a4');

        return $pdf->download("kontrak-{$contract->id}.pdf");
    }

    private function ensureTenantOwnsContract(User $tenant, Contract $contract): void
    {
        abort_if($contract->tenant_id !== $tenant->id, 403, 'Kontrak tidak ditemukan untuk akun ini.');
    }

    private function determineNextCoverageMonth(Contract $contract): Carbon
    {
        $lastInvoice = $contract->invoices()
            ->orderByDesc('coverage_end_year')
            ->orderByDesc('coverage_end_month')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->orderByDesc('id')
            ->first();

        if ($lastInvoice) {
            $lastYear = $lastInvoice->coverage_end_year ?? $lastInvoice->period_year;
            $lastMonth = $lastInvoice->coverage_end_month ?? $lastInvoice->period_month;

            if ($lastYear && $lastMonth) {
                return Carbon::create($lastYear, $lastMonth, 1)->startOfMonth()->addMonth();
            }
        }

        if ($contract->start_date) {
            return $contract->start_date->copy()->startOfMonth();
        }

        return Carbon::now()->startOfMonth();
    }
}
