<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Services\OwnerWalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ManualPaymentController extends Controller
{
    public function __construct(private readonly OwnerWalletService $walletService)
    {
    }

    public function index(Request $request): View
    {
        /** @var User $owner */
        $owner = $request->user();

        $payments = Payment::query()
            ->where('payment_type', 'manual_bank_transfer')
            ->where('status', 'waiting_verification')
            ->whereHas('invoice.contract.room.roomType.property', fn ($query) => $query->where('owner_id', $owner->id))
            ->with([
                'invoice.contract.room.roomType.property',
                'submitter:id,name,email',
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('owner.manual-payments.index', [
            'payments' => $payments,
        ]);
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        /** @var User $owner */
        $owner = $request->user();

        abort_unless($this->paymentBelongsToOwner($payment, $owner->id), 403);

        if ($payment->status !== 'waiting_verification') {
            return back()->with('status', __('Pembayaran sudah diproses sebelumnya.'));
        }

        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($payment, $validated, $owner): void {
            if ($validated['action'] === 'approve') {
                $payment->update([
                    'status' => 'success',
                    'verified_by' => $owner->id,
                    'verified_at' => now(),
                    'rejection_reason' => null,
                    'paid_at' => now(),
                ]);

                $payment->invoice?->markAsPaid();
                $this->walletService->creditFromPayment($payment);
            } else {
                $payment->update([
                    'status' => 'rejected',
                    'verified_by' => $owner->id,
                    'verified_at' => now(),
                    'rejection_reason' => $validated['rejection_reason'],
                ]);

                $payment->invoice?->update(['status' => 'unpaid']);
            }
        });

        $message = $validated['action'] === 'approve'
            ? __('Pembayaran manual disetujui.')
            : __('Pembayaran manual ditolak.');

        return back()->with('status', $message);
    }

    private function paymentBelongsToOwner(Payment $payment, int $ownerId): bool
    {
        $property = $payment->invoice?->contract?->room?->roomType?->property;

        return $property?->owner_id === $ownerId && $payment->payment_type === 'manual_bank_transfer';
    }
}
