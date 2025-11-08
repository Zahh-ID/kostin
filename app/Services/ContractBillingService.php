<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Support\Carbon;

class ContractBillingService
{
    /**
     * Ensure a contract always has at least one invoice when it is first approved.
     */
    public function ensureInitialInvoice(Contract $contract, int $monthsCount = 1): ?Invoice
    {
        if ($contract->invoices()->exists()) {
            return null;
        }

        return $this->createInvoiceForNextCoverage($contract, $monthsCount);
    }

    /**
     * Create an invoice for the next unpaid coverage window.
     */
    public function createInvoiceForNextCoverage(Contract $contract, int $monthsCount = 1): Invoice
    {
        $monthsCount = max(1, $monthsCount);
        $coverageStart = $this->determineNextCoverageStart($contract);

        return $this->createInvoice($contract, $monthsCount, $coverageStart);
    }

    /**
     * Cancel any invoices that have not been fully paid yet.
     */
    public function cancelOutstandingInvoices(Contract $contract): void
    {
        $contract->outstandingInvoices()->update([
            'status' => Invoice::STATUS_CANCELED,
            'qris_payload' => null,
        ]);
    }

    /**
     * Determine the next coverage month for a contract.
     */
    public function determineNextCoverageStart(Contract $contract): Carbon
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
                return Carbon::create($lastYear, $lastMonth, 1)
                    ->startOfMonth()
                    ->addMonth();
            }
        }

        if ($contract->start_date) {
            return $contract->start_date->copy()->startOfMonth();
        }

        return Carbon::now()->startOfMonth();
    }

    /**
     * Create an invoice record with the provided coverage start.
     */
    private function createInvoice(Contract $contract, int $monthsCount, Carbon $coverageStart): Invoice
    {
        $coverageStart = $coverageStart->copy()->startOfMonth();
        $coverageEnd = $coverageStart->copy()->addMonths($monthsCount - 1);
        $billingDay = max(1, min(28, (int) ($contract->billing_day ?? 1)));

        $dueDate = $coverageStart->copy()->day(min($billingDay, $coverageStart->daysInMonth));
        $contractStart = $contract->start_date ?: $coverageStart;

        if ($dueDate->lt($contractStart)) {
            $nextMonth = $coverageStart->copy()->addMonth();
            $dueDate = $nextMonth->copy()->day(min($billingDay, $nextMonth->daysInMonth));
        }

        $amount = ($contract->price_per_month ?? 0) * $monthsCount;

        return $contract->invoices()->create([
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
            'status' => Invoice::STATUS_UNPAID,
        ]);
    }
}
