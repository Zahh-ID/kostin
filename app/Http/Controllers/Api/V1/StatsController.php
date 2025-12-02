<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Stats", description="Aggregated public stats for dashboards")
 */
class StatsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/stats",
     *     tags={"Stats"},
     *     summary="Get aggregated dashboard stats",
     *     @OA\Response(
     *         response=200,
     *         description="Aggregated stats",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="payments",
     *                 type="object",
     *                 @OA\Property(property="success_rate", type="number", format="float"),
     *                 @OA\Property(property="success_count", type="integer"),
     *                 @OA\Property(property="total_count", type="integer"),
     *                 @OA\Property(property="qris_count", type="integer"),
     *                 @OA\Property(property="manual_count", type="integer")
     *             ),
     *             @OA\Property(
     *                 property="contracts",
     *                 type="object",
     *                 @OA\Property(property="active_count", type="integer")
     *             ),
     *             @OA\Property(
     *                 property="tickets",
     *                 type="object",
     *                 @OA\Property(property="live_count", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function __invoke(): JsonResponse
    {
        $successPayments = Payment::query()->where('status', 'success')->count();
        $totalPayments = Payment::query()->count();
        $successRate = $totalPayments > 0 ? round(($successPayments / $totalPayments) * 100, 2) : 0;

        return response()->json([
            'payments' => [
                'success_rate' => $successRate,
                'success_count' => $successPayments,
                'total_count' => $totalPayments,
                'qris_count' => Payment::query()->where('payment_type', 'qris')->count(),
                'manual_count' => Payment::query()->where('payment_type', 'manual_bank_transfer')->count(),
            ],
            'contracts' => [
                'active_count' => Contract::query()->where('status', Contract::STATUS_ACTIVE)->count(),
            ],
            'tickets' => [
                'live_count' => Ticket::query()
                    ->whereIn('status', [
                        Ticket::STATUS_OPEN,
                        Ticket::STATUS_IN_REVIEW,
                        Ticket::STATUS_ESCALATED,
                    ])->count(),
            ],
        ]);
    }
}
