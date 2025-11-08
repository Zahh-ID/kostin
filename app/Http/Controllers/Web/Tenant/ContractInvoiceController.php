<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\InvoiceCreateRequest;
use App\Models\Contract;
use App\Services\ContractBillingService;
use Illuminate\Http\RedirectResponse;

class ContractInvoiceController extends Controller
{
    public function __construct(
        private readonly ContractBillingService $contractBillingService,
    ) {
    }

    public function store(InvoiceCreateRequest $request, Contract $contract): RedirectResponse
    {
        $tenant = $request->user();
        abort_if($contract->tenant_id !== $tenant->id, 403, __('Kontrak tidak ditemukan.'));
        abort_unless($contract->status === 'active', 422, __('Kontrak tidak aktif.'));

        $monthsCount = max(1, min(12, (int) $request->input('months_count', 1)));

        $hasOpenInvoice = $contract->invoices()
            ->whereIn('status', ['unpaid', 'pending_verification', 'waiting_verification'])
            ->exists();

        if ($hasOpenInvoice) {
            return back()
                ->withInput($request->only('months_count'))
                ->withErrors(['months_count' => __('Selesaikan invoice yang masih aktif sebelum membuat invoice baru.')]);
        }

        $invoice = $this->contractBillingService->createInvoiceForNextCoverage($contract, $monthsCount);

        return redirect()
            ->route('tenant.invoices.show', $invoice)
            ->with('status', __('Invoice baru berhasil dibuat untuk :months bulan.', ['months' => $monthsCount]));
    }
}
