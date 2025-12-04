<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\OwnerWalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookSimulatorController extends Controller
{
    public function __construct(
        protected OwnerWalletService $walletService
    ) {
    }

    /**
     * Simulate Midtrans Webhook (Admin Only)
     * 
     * POST /api/v1/admin/webhook/midtrans
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'transaction_status' => 'required|string|in:capture,settlement,pending,deny,cancel,expire',
        ]);

        $orderId = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');

        $payment = Payment::where('order_id', $orderId)->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found for order_id: ' . $orderId
            ], 404);
        }

        Log::info('Simulating Midtrans Webhook', [
            'order_id' => $orderId,
            'status' => $transactionStatus,
            'admin_id' => $request->user()->id
        ]);

        // Update payment status
        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            // Payment success
            $payment->update([
                'status' => 'success',
                'transaction_status' => $transactionStatus,
                'settlement_time' => now(),
            ]);

            // Update invoice
            $invoice = $payment->invoice;
            if ($invoice) {
                $invoice->markAsPaid();
            }

            $this->walletService->creditFromPayment($payment);

        } elseif ($transactionStatus === 'deny' || $transactionStatus === 'cancel' || $transactionStatus === 'expire') {
            // Payment failed
            $payment->update([
                'status' => 'failed',
                'transaction_status' => $transactionStatus,
            ]);

        } elseif ($transactionStatus === 'pending') {
            // Still pending
            $payment->update([
                'transaction_status' => $transactionStatus,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully',
            'data' => [
                'order_id' => $orderId,
                'status' => $payment->status,
                'transaction_status' => $payment->transaction_status
            ]
        ]);
    }
}
