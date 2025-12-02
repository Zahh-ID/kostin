<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\Owner\OwnerManualPaymentResource;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManualPaymentIndexController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $owner */
        $owner = $request->user();

        $payments = Payment::query()
            ->with(['invoice.contract.room.roomType.property', 'submitter'])
            ->whereHas('invoice.contract.room.roomType.property', fn ($query) => $query->where('owner_id', $owner->id))
            ->latest()
            ->limit(30)
            ->get();

        return response()->json([
            'data' => OwnerManualPaymentResource::collection($payments),
        ]);
    }
}
