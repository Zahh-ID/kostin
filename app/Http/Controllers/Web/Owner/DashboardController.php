<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\SharedTask;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $owner = $request->user();

        $properties = $owner->properties()
            ->with(['roomTypes.rooms', 'sharedTasks' => fn ($query) => $query->latest()->limit(3)])
            ->get();

        $roomCount = Room::query()
            ->whereHas('roomType.property', fn ($query) => $query->where('owner_id', $owner->id))
            ->count();

        $activeContracts = Contract::query()
            ->where('status', 'active')
            ->whereHas('room.roomType.property', fn ($query) => $query->where('owner_id', $owner->id))
            ->count();

        $overdueInvoices = Invoice::query()
            ->whereIn('status', ['unpaid', 'overdue'])
            ->whereHas('contract.room.roomType.property', fn ($query) => $query->where('owner_id', $owner->id))
            ->orderBy('due_date')
            ->limit(5)
            ->with(['contract.room.roomType', 'payments' => fn ($query) => $query->latest()->limit(1)])
            ->get();

        $upcomingTasks = SharedTask::query()
            ->whereHas('property', fn ($query) => $query->where('owner_id', $owner->id))
            ->orderBy('next_run_at')
            ->limit(5)
            ->with(['property'])
            ->get();

        $monthlyIncome = Invoice::query()
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as period, SUM(total) as total')
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->whereHas('contract.room.roomType.property', fn ($query) => $query->where('owner_id', $owner->id))
            ->where('paid_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('period')
            ->pluck('total', 'period');

        $monthlyLabels = collect(range(0, 5))->map(function (int $index) {
            $month = Carbon::now()->subMonths(5 - $index)->startOfMonth();

            return [
                'label' => $month->translatedFormat('M Y'),
                'key' => $month->format('Y-m'),
            ];
        });

        $incomeTrend = [
            'labels' => $monthlyLabels->pluck('label'),
            'data' => $monthlyLabels->map(fn (array $item) => (int) $monthlyIncome->get($item['key'], 0)),
        ];

        $roomStatusBreakdown = Room::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->whereHas('roomType.property', fn ($query) => $query->where('owner_id', $owner->id))
            ->groupBy('status')
            ->pluck('total', 'status');

        $contractExpirations = Contract::query()
            ->selectRaw('DATE_FORMAT(start_date, "%Y-%m") as period, COUNT(*) as total')
            ->where('status', 'active')
            ->whereHas('room.roomType.property', fn ($query) => $query->where('owner_id', $owner->id))
            ->groupBy('period')
            ->orderBy('period')
            ->limit(6)
            ->pluck('total', 'period');

        $nextExpiringContracts = Contract::query()
            ->whereHas('room.roomType.property', fn ($query) => $query->where('owner_id', $owner->id))
            ->where('status', 'active')
            ->whereBetween('end_date', [Carbon::now(), Carbon::now()->addMonths(3)])
            ->with(['tenant', 'room.roomType.property'])
            ->orderBy('end_date')
            ->limit(5)
            ->get();

        return view('owner.dashboard', [
            'properties' => $properties,
            'roomCount' => $roomCount,
            'activeContracts' => $activeContracts,
            'overdueInvoices' => $overdueInvoices,
            'upcomingTasks' => $upcomingTasks instanceof Collection ? $upcomingTasks : collect($upcomingTasks),
            'incomeTrend' => $incomeTrend,
            'roomStatusBreakdown' => $roomStatusBreakdown,
            'contractExpirations' => $contractExpirations,
            'nextExpiringContracts' => $nextExpiringContracts,
        ]);
    }
}
