<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\Owner\OwnerWalletResource;
use App\Models\OwnerWallet;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $owner */
        $owner = $request->user();

        $wallet = OwnerWallet::query()->firstOrCreate(
            ['owner_id' => $owner->id],
            ['balance' => 0]
        );

        $pendingSettlement = Payment::query()
            ->whereHas('invoice.contract.room.roomType.property', fn($query) => $query->where('owner_id', $owner->id))
            ->pending()
            ->sum('amount');

        $pendingWithdrawals = $wallet->transactions()
            ->where('type', 'withdrawal')
            ->count();

        return response()->json([
            'available' => (int) $wallet->balance,
            'pending' => (int) $pendingSettlement,
            'withdrawals' => $pendingWithdrawals,
        ]);
    }
}
