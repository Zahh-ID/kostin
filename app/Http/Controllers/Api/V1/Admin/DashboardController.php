<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $now = Carbon::now();

        $revenueThisMonth = Payment::query()
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->success()
            ->sum('amount');

        $registrationsThisMonth = User::query()
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $pendingModerations = Property::query()
            ->where('status', 'pending')
            ->count();

        $propertyStatusCounts = Property::query()
            ->select('status', DB::raw('count(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $ticketsOpen = Ticket::query()
            ->whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_IN_REVIEW, Ticket::STATUS_ESCALATED])
            ->where(function ($query) {
                $query->whereNull('related_type')
                    ->orWhereNotIn('related_type', [
                        \App\Models\Property::class,
                        \App\Models\Room::class,
                        \App\Models\RoomType::class,
                        \App\Models\Contract::class,
                        \App\Models\Invoice::class,
                    ]);
            })
            ->count();

        $invoices = Invoice::query()
            ->select('status', DB::raw('count(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $usersByRole = User::query()
            ->select('role', DB::raw('count(*) as aggregate'))
            ->groupBy('role')
            ->pluck('aggregate', 'role');

        $revenueTrend = $this->buildMonthlyTrend(fn(Carbon $start, Carbon $end) => Payment::query()
            ->whereBetween('created_at', [$start, $end])
            ->success()
            ->sum('amount'));

        $registrationsTrend = $this->buildMonthlyTrend(fn(Carbon $start, Carbon $end) => User::query()
            ->whereBetween('created_at', [$start, $end])
            ->count());

        return response()->json([
            'revenue_this_month' => (int) $revenueThisMonth,
            'registrations_this_month' => $registrationsThisMonth,
            'pending_moderations' => $pendingModerations,
            'tickets_open' => $ticketsOpen,
            'invoices' => [
                'unpaid' => (int) ($invoices['unpaid'] ?? 0),
                'paid' => (int) ($invoices['paid'] ?? 0),
                'pending_verification' => (int) ($invoices['pending_verification'] ?? 0),
                'overdue' => (int) ($invoices['overdue'] ?? 0),
            ],
            'users' => [
                'admin' => (int) ($usersByRole[User::ROLE_ADMIN] ?? 0),
                'owner' => (int) ($usersByRole[User::ROLE_OWNER] ?? 0),
                'tenant' => (int) ($usersByRole[User::ROLE_TENANT] ?? 0),
            ],
            'revenue_trend' => $revenueTrend,
            'registrations_trend' => $registrationsTrend,
            'approved_properties' => (int) ($propertyStatusCounts['approved'] ?? 0),
            'rejected_properties' => (int) ($propertyStatusCounts['rejected'] ?? 0),
        ]);
    }

    /**
     * @param callable(Carbon, Carbon): int $callback
     * @return array<int, array<string, int|string>>
     */
    private function buildMonthlyTrend(callable $callback): array
    {
        $months = [];
        $now = Carbon::now()->startOfMonth();

        for ($i = 5; $i >= 0; $i--) {
            $start = (clone $now)->subMonths($i);
            $end = (clone $start)->endOfMonth();
            $value = (int) $callback($start, $end);
            $months[] = [
                'label' => $start->translatedFormat('M'),
                'value' => $value,
            ];
        }

        return $months;
    }
}
