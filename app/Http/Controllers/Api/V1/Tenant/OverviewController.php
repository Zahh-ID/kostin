<?php

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $tenant = $request->user();

        if ($tenant === null || $tenant->role !== 'tenant') {
            abort(403);
        }

        $invoiceQuery = Invoice::query()
            ->whereHas('contract', fn ($query) => $query->where('tenant_id', $tenant->id));

        $ticketsQuery = Ticket::query()->where('reporter_id', $tenant->id);

        $data = [
            'invoices' => [
                'unpaid' => (clone $invoiceQuery)->where('status', Invoice::STATUS_UNPAID)->count(),
                'overdue' => (clone $invoiceQuery)->where('status', Invoice::STATUS_OVERDUE)->count(),
                'pending_verification' => (clone $invoiceQuery)->where('status', Invoice::STATUS_PENDING_VERIFICATION)->count(),
                'paid' => (clone $invoiceQuery)->where('status', Invoice::STATUS_PAID)->count(),
            ],
            'contracts' => [
                'active' => Contract::query()
                    ->where('tenant_id', $tenant->id)
                    ->where('status', Contract::STATUS_ACTIVE)
                    ->count(),
            ],
            'tickets' => [
                'open' => $ticketsQuery
                    ->whereIn('status', [
                        Ticket::STATUS_OPEN,
                        Ticket::STATUS_IN_REVIEW,
                        Ticket::STATUS_ESCALATED,
                    ])->count(),
            ],
            'wishlist' => [
                'count' => WishlistItem::query()->where('user_id', $tenant->id)->count(),
            ],
        ];

        return response()->json($data);
    }
}
