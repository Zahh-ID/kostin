<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use App\Services\OwnerWalletService;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $midtrans;

    public function __construct(
        MidtransService $midtrans,
        protected OwnerWalletService $walletService
    ) {
        $this->midtrans = $midtrans;
    }

    /**
     * Handle Midtrans Notification Webhook
     * 
     * POST /api/webhook/midtrans
     */
    public function handleNotification(Request $request)
    {
        try {
            $notification = $request->all();

            Log::info('Midtrans Webhook Received', $notification);

            // Allow manual success trigger from simulator/dev
            if ($request->boolean('force_success')) {
                $notification['transaction_status'] = $notification['transaction_status'] ?? 'settlement';
                $notification['status_code'] = $notification['status_code'] ?? '200';
                $notification['fraud_status'] = $notification['fraud_status'] ?? 'accept';
            }

            // Extract data
            $orderId = $notification['order_id'] ?? null;
            $statusCode = $notification['status_code'] ?? null;
            $grossAmount = $notification['gross_amount'] ?? null;
            $signatureKey = $notification['signature_key'] ?? null;
            $transactionStatus = $notification['transaction_status'] ?? null;
            $fraudStatus = $notification['fraud_status'] ?? null;

            // Verify signature
            if (!$this->midtrans->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
                Log::warning('Invalid signature', [
                    'order_id' => $orderId
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid signature'
                ], 401);
            }

            // Find payment
            $payment = Payment::where('order_id', $orderId)->first();

            if (!$payment) {
                Log::warning('Payment not found', [
                    'order_id' => $orderId
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

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
                $invoice?->markAsPaid();
                $this->walletService->creditFromPayment($payment);

                Log::info('Payment success', [
                    'payment_id' => $payment->id,
                    'order_id' => $orderId
                ]);

            } elseif ($transactionStatus === 'deny' || $transactionStatus === 'cancel' || $transactionStatus === 'expire') {
                // Payment failed
                $payment->update([
                    'status' => 'failed',
                    'transaction_status' => $transactionStatus,
                ]);

                Log::info('Payment failed', [
                    'payment_id' => $payment->id,
                    'order_id' => $orderId,
                    'reason' => $transactionStatus
                ]);

            } elseif ($transactionStatus === 'pending') {
                // Still pending
                $payment->update([
                    'transaction_status' => $transactionStatus,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification processed'
            ]);

        } catch (\Exception $e) {
            Log::error('Webhook Handler Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }
}
