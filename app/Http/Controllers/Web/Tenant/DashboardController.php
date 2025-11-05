<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $activeContracts = $user->contracts()
            ->with(['room.roomType.property'])
            ->where('status', 'active')
            ->get();

        $dueInvoices = $user->invoices()
            ->with(['contract.room.roomType.property'])
            ->whereIn('invoices.status', ['unpaid', 'overdue'])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $recentPayments = $user->invoices()
            ->with(['payments' => fn ($query) => $query->latest()->limit(1)])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->flatMap(fn ($invoice) => $invoice->payments);

        $totalOutstanding = $user->invoices()
            ->whereIn('invoices.status', ['unpaid', 'overdue'])
            ->sum('total');

        $successfulPayments = $user->invoices()
            ->with(['payments' => fn ($query) => $query->where('status', 'success')])
            ->get()
            ->flatMap(fn ($invoice) => $invoice->payments)
            ->map(function ($payment) {
                $timestamp = $payment->paid_at ?? $payment->created_at;
                $payment->payment_month = optional($timestamp)->format('Y-m');

                return $payment;
            });

        $periods = collect(range(0, 5))->map(function (int $index) {
            $month = Carbon::now()->subMonths(5 - $index)->startOfMonth();

            return [
                'label' => $month->translatedFormat('M Y'),
                'key' => $month->format('Y-m'),
            ];
        });

        $paymentTrend = [
            'labels' => $periods->pluck('label'),
            'data' => $periods->map(function (array $period) use ($successfulPayments) {
                return (int) $successfulPayments
                    ->where('payment_month', $period['key'])
                    ->sum('amount');
            }),
        ];

        $invoiceStatusBreakdown = Invoice::query()
            ->whereHas('contract', fn ($query) => $query->where('tenant_id', $user->id))
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $nextDueInvoice = $dueInvoices->first();

        return view('tenant.dashboard', [
            'activeContracts' => $activeContracts,
            'dueInvoices' => $dueInvoices,
            'recentPayments' => $recentPayments instanceof Collection ? $recentPayments : collect($recentPayments),
            'totalOutstanding' => $totalOutstanding,
            'paymentTrend' => $paymentTrend,
            'invoiceStatusBreakdown' => $invoiceStatusBreakdown,
            'nextDueInvoice' => $nextDueInvoice,
        ]);
    }
}
