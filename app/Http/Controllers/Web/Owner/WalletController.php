<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\OwnerWallet;
use App\Services\OwnerWalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function __construct(private readonly OwnerWalletService $walletService)
    {
    }

    public function index(Request $request): View
    {
        $wallet = OwnerWallet::firstOrCreate(
            ['owner_id' => $request->user()->id],
            ['balance' => 0]
        );

        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('owner.wallet.index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }

    public function withdraw(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $wallet = OwnerWallet::firstOrCreate(
            ['owner_id' => $request->user()->id],
            ['balance' => 0]
        );

        try {
            $this->walletService->withdraw($wallet, (float) $validated['amount'], $validated['notes'] ?? null);
        } catch (\InvalidArgumentException $exception) {
            return back()->withErrors(['amount' => $exception->getMessage()])->withInput();
        }

        return back()->with('status', __('Permintaan pencairan tersimpan. Tim finance akan memprosesnya.'));
    }
}
