<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $statisticCards = [
            'properties_total' => Property::count(),
            'properties_pending' => Property::where('status', 'pending')->count(),
            'tenants' => User::where('role', User::ROLE_TENANT)->count(),
            'owners' => User::where('role', User::ROLE_OWNER)->count(),
            'active_contracts' => Contract::where('status', 'active')->count(),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
        ];

        $recentUsers = User::query()
            ->latest()
            ->limit(5)
            ->get(['id', 'name', 'email', 'role', 'created_at']);

        $recentInvoices = Invoice::query()
            ->latest()
            ->limit(5)
            ->with(['contract.tenant:id,name', 'contract.room.roomType.property:id,name'])
            ->get();

        $monthlyBuckets = collect(range(0, 5))->map(function (int $index) {
            $month = Carbon::now()->subMonths(5 - $index)->startOfMonth();

            return [
                'label' => $month->translatedFormat('M Y'),
                'key' => $month->format('Y-m'),
            ];
        });

        $registrationCounts = User::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('period')
            ->pluck('total', 'period');

        $registrationTrend = [
            'labels' => $monthlyBuckets->pluck('label'),
            'data' => $monthlyBuckets->map(fn (array $bucket) => (int) $registrationCounts->get($bucket['key'], 0)),
        ];

        $revenueBuckets = Invoice::query()
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as period, SUM(total) as total')
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('period')
            ->pluck('total', 'period');

        $revenueTrend = [
            'labels' => $monthlyBuckets->pluck('label'),
            'data' => $monthlyBuckets->map(fn (array $bucket) => (int) $revenueBuckets->get($bucket['key'], 0)),
        ];

        $propertyStatusBreakdown = Property::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $ticketStatusBreakdown = Ticket::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingModerations = Property::query()
            ->where('status', 'pending')
            ->with(['owner:id,name,email'])
            ->orderBy('updated_at')
            ->limit(5)
            ->get();

        $recentTickets = Ticket::query()
            ->latest()
            ->limit(5)
            ->with(['reporter:id,name', 'assignee:id,name'])
            ->get();

        return view('admin.dashboard', [
            'stats' => $statisticCards,
            'recentUsers' => $recentUsers,
            'recentInvoices' => $recentInvoices,
            'registrationTrend' => $registrationTrend,
            'revenueTrend' => $revenueTrend,
            'propertyStatusBreakdown' => $propertyStatusBreakdown,
            'ticketStatusBreakdown' => $ticketStatusBreakdown,
            'pendingModerations' => $pendingModerations,
            'recentTickets' => $recentTickets,
        ]);
    }
}
