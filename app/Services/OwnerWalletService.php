<?php

namespace App\Services;

use App\Models\OwnerWallet;
use App\Models\OwnerWalletTransaction;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class OwnerWalletService
{
    public function creditFromPayment(?Payment $payment): void
    {
        if (! $payment) {
            return;
        }

        $player = optional($payment->invoice)
            ?->contract
            ?->room
            ?->roomType
            ?->property
            ?->owner;

        if (! $player) {
            return;
        }

        DB::transaction(function () use ($player, $payment): void {
            $wallet = OwnerWallet::firstOrCreate(
                ['owner_id' => $player->id],
                ['balance' => 0]
            );

            $existing = $wallet->transactions()
                ->where('payment_id', $payment->id)
                ->exists();

            if ($existing) {
                return;
            }

            $amount = $payment->amount ?? 0;

            $wallet->transactions()->create([
                'payment_id' => $payment->id,
                'type' => 'credit',
                'amount' => $amount,
                'description' => __('Pembayaran invoice #:invoice', ['invoice' => optional($payment->invoice)->id]),
            ]);

            $wallet->increment('balance', $amount);
        });
    }

    public function withdraw(OwnerWallet $wallet, float $amount, ?string $notes = null): OwnerWalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive.');
        }

        if ($amount > $wallet->balance) {
            throw new \InvalidArgumentException('Insufficient balance.');
        }

        return DB::transaction(function () use ($wallet, $amount, $notes): OwnerWalletTransaction {
            $transaction = $wallet->transactions()->create([
                'type' => 'debit',
                'amount' => $amount,
                'description' => __('Permintaan pencairan dana'),
                'metadata' => array_filter([
                    'notes' => $notes,
                ]),
            ]);

            $wallet->decrement('balance', $amount);

            return $transaction;
        });
    }
}
