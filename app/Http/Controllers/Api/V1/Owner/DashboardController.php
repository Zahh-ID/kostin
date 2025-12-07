<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentalApplication;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $owner */
        $owner = $request->user();

        $propertyIds = Property::query()->where('owner_id', $owner->id)->pluck('id');

        $revenueThisMonth = Payment::query()
            ->whereHas('invoice.contract.room.roomType.property', fn($query) => $query->where('owner_id', $owner->id))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->success()
            ->sum('amount');

        $registrationsThisMonth = Contract::query()
            ->whereHas('room.roomType.property', fn($query) => $query->where('owner_id', $owner->id))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $roomTypesCount = RoomType::query()
            ->whereIn('property_id', $propertyIds)
            ->count();

        $rooms = Room::query()
            ->whereHas('roomType.property', fn($query) => $query->where('owner_id', $owner->id))
            ->select('status', DB::raw('count(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $contracts = Contract::query()
            ->whereHas('room.roomType.property', fn($query) => $query->where('owner_id', $owner->id))
            ->get();

        $manualPaymentsPending = Payment::query()
            ->whereHas('invoice.contract.room.roomType.property', fn($query) => $query->where('owner_id', $owner->id))
            ->where('payment_type', '!=', 'qris')
            ->pending()
            ->count();

        $applications = RentalApplication::query()
            ->whereIn('property_id', $propertyIds)
            ->select('status', DB::raw('count(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $ticketsOpen = Ticket::query()
            ->whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_IN_REVIEW, Ticket::STATUS_ESCALATED])
            ->where('related_type', Property::class)
            ->whereHasMorph(
                'related',
                [Property::class],
                fn($query) => $query->where('owner_id', $owner->id)
            )
            ->count();

        $revenueTrend = $this->buildMonthlyTrend(
            fn(Carbon $start, Carbon $end) => Payment::query()
                ->whereHas('invoice.contract.room.roomType.property', fn($query) => $query->where('owner_id', $owner->id))
                ->whereBetween('created_at', [$start, $end])
                ->success()
                ->sum('amount'),
        );

        $contractsStartedTrend = $this->buildMonthlyTrend(
            fn(Carbon $start, Carbon $end) => Contract::query()
                ->whereHas('room.roomType.property', fn($query) => $query->where('owner_id', $owner->id))
                ->whereBetween('start_date', [$start, $end])
                ->count(),
        );

        return response()->json([
            'revenue_this_month' => (int) $revenueThisMonth,
            'registrations_this_month' => $registrationsThisMonth,
            'room_types' => $roomTypesCount,
            'rooms' => [
                'occupied' => (int) ($rooms['occupied'] ?? 0),
                'available' => (int) ($rooms['available'] ?? 0),
                'maintenance' => (int) ($rooms['maintenance'] ?? 0),
                'total' => (int) $rooms->sum(),
            ],
            'contracts' => [
                'active' => $contracts->where('status', 'active')->count(),
                'ending_soon' => $contracts->where('status', 'ending_soon')->count(),
            ],
            'manual_payments_pending' => $manualPaymentsPending,
            'applications' => [
                'pending' => (int) ($applications['pending'] ?? 0),
                'approved' => (int) ($applications['approved'] ?? 0),
                'rejected' => (int) ($applications['rejected'] ?? 0),
            ],
            'tickets_open' => $ticketsOpen,
            'revenue_trend' => $revenueTrend,
            'contracts_started_trend' => $contractsStartedTrend,
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
