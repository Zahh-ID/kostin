<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\OwnerWallet;
use App\Models\OwnerWalletTransaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletWithdrawController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_holder' => 'required|string',
        ]);

        /** @var User $owner */
        $owner = $request->user();

        $wallet = OwnerWallet::firstOrCreate(['owner_id' => $owner->id]);

        if ($wallet->balance < $request->amount) {
            return response()->json(['message' => 'Saldo tidak mencukupi'], 422);
        }

        return DB::transaction(function () use ($wallet, $request) {
            // Deduct balance
            $wallet->decrement('balance', $request->amount);

            // Create transaction record
            $wallet->transactions()->create([
                'type' => 'withdrawal',
                'amount' => $request->amount,
                'status' => 'pending',
                'description' => "Penarikan dana ke {$request->bank_name} ({$request->account_number})",
                'metadata' => [
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'account_holder' => $request->account_holder,
                ],
            ]);

            return response()->json(['message' => 'Permintaan penarikan berhasil dibuat']);
        });
    }
}
