<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\User;
use App\Services\ContractBillingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function __construct(
        private readonly ContractBillingService $contractBillingService,
    ) {}

    public function index(Request $request): View
    {
        /** @var User $tenant */
        $tenant = $request->user();

        $withHistory = $request->boolean('history');

        /** @var LengthAwarePaginator $contracts */
        $query = $tenant->contracts()
            ->with([
                'room.roomType.property',
                'invoices' => fn ($query) => $query->latest('due_date')->limit(1),
            ])
            ->orderByDesc('status')
            ->latest('start_date');

        if (! $withHistory) {
            $query->where('status', \App\Models\Contract::STATUS_ACTIVE);
        }

        $contracts = $query->paginate(10)->withQueryString();

        return view('tenant.contracts.index', [
            'contracts' => $contracts,
            'withHistory' => $withHistory,
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
            'terminationRequests' => fn ($query) => $query->latest(),
        ]);

        $nextCoverageStart = $this->contractBillingService->determineNextCoverageStart($contract);
        $daysToEnd = $contract->end_date
            ? max(0, Carbon::now()->diffInDays($contract->end_date, false))
            : null;
        $outstandingInvoicesCount = $contract->outstandingInvoicesCount();
        $primaryInvoice = $contract->invoices->first(function ($invoice) {
            return in_array($invoice->status, \App\Models\Invoice::OUTSTANDING_STATUSES, true);
        });
        $terminationBlockedReason = null;

        if ($contract->status !== \App\Models\Contract::STATUS_ACTIVE) {
            $terminationBlockedReason = __('Kontrak tidak aktif.');
        } elseif ($outstandingInvoicesCount > 0) {
            $terminationBlockedReason = __('Selesaikan :count tagihan aktif sebelum mengajukan pengakhiran.', [
                'count' => $outstandingInvoicesCount,
            ]);
        }

        return view('tenant.contracts.show', [
            'contract' => $contract,
            'nextCoverageStart' => $nextCoverageStart,
            'daysToEnd' => $daysToEnd,
            'latestTerminationRequest' => $contract->terminationRequests->first(),
            'canRequestTermination' => $terminationBlockedReason === null,
            'terminationBlockedReason' => $terminationBlockedReason,
            'outstandingInvoicesCount' => $outstandingInvoicesCount,
            'primaryInvoice' => $primaryInvoice,
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
}
