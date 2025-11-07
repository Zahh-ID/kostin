<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\InvoiceCreateRequest;
use App\Models\Contract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class ContractInvoiceController extends Controller
{
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

        $coverageStart = $this->determineNextCoverageMonth($contract);
        $coverageEnd = (clone $coverageStart)->addMonths($monthsCount - 1);

        $amount = ($contract->price_per_month ?? 0) * $monthsCount;
        $dueDate = (clone $coverageStart)->day(
            min($contract->billing_day ?? 1, $coverageStart->daysInMonth)
        );

        $invoice = $contract->invoices()->create([
            'period_month' => $coverageStart->month,
            'period_year' => $coverageStart->year,
            'months_count' => $monthsCount,
            'coverage_start_month' => $coverageStart->month,
            'coverage_start_year' => $coverageStart->year,
            'coverage_end_month' => $coverageEnd->month,
            'coverage_end_year' => $coverageEnd->year,
            'due_date' => $dueDate,
            'amount' => $amount,
            'late_fee' => 0,
            'total' => $amount,
            'status' => 'unpaid',
        ]);

        return redirect()
            ->route('tenant.invoices.show', $invoice)
            ->with('status', __('Invoice baru berhasil dibuat untuk :months bulan.', ['months' => $monthsCount]));
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
