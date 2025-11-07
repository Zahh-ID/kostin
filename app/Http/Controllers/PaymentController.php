<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    /**
     * Create QRIS Payment
     * 
     * POST /api/payment/create-qris
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createQrisPayment(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
                'amount' => 'required|numeric|min:1',
                'description' => 'nullable|string',
            ]);

            // Get invoice
            $invoice = Invoice::findOrFail($validated['invoice_id']);

            // Check if already paid
            if ($invoice->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice sudah dibayar'
                ], 400);
            }

            // Generate unique order ID
            $orderId = 'ORDER-' . time() . '-' . Str::random(8);

            // Get user
            $user = $request->user();

            // Prepare transaction params
            $params = [
                'order_id' => $orderId,
                'gross_amount' => (int) $validated['amount'],
                'customer_name' => $user->name ?? 'Customer',
                'customer_email' => $user->email ?? '',
                'customer_phone' => $user->phone ?? '',
                'item_details' => [
                    [
                        'id' => $invoice->id,
                        'price' => (int) $validated['amount'],
                        'quantity' => 1,
                        'name' => $validated['description'] ?? 'Payment for Invoice ' . $invoice->id,
                    ]
                ],
            ];

            // Create Midtrans transaction
            $result = $this->midtrans->createQrisTransaction($params);

            // Save payment record to database
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => $user->id,
                'midtrans_order_id' => $result['order_id'] ?? $orderId,
                'order_id' => $result['order_id'],
                'transaction_id' => $result['transaction_id'],
                'payment_type' => 'qris',
                'amount' => $validated['amount'],
                'status' => 'pending',
                'qris_string' => $result['qris_string'],
                'midtrans_response' => json_encode($result['raw_response']),
                'raw_webhook_json' => $result['raw_response'],
            ]);

            Log::info('QRIS payment created', [
                'payment_id' => $payment->id,
                'order_id' => $orderId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'QRIS payment created successfully',
                'data' => [
                    'payment_id' => $payment->id,
                    'order_id' => $result['order_id'],
                    'transaction_id' => $result['transaction_id'],
                    'qris_string' => $result['qris_string'],
                    'amount' => $validated['amount'],
                    'status' => 'pending',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Create QRIS Payment Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pembayaran QRIS',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create Bank Transfer Payment
     * 
     * POST /api/payment/create-bank-transfer
     */
    public function createBankTransferPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
                'amount' => 'required|numeric|min:1',
                'bank' => 'required|in:bca,bni,bri,permata',
                'description' => 'nullable|string',
            ]);

            $invoice = Invoice::findOrFail($validated['invoice_id']);
            $user = $request->user();

            if ($invoice->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice sudah dibayar'
                ], 400);
            }

            $orderId = 'ORDER-' . time() . '-' . Str::random(8);

            $params = [
                'order_id' => $orderId,
                'gross_amount' => (int) $validated['amount'],
                'bank' => $validated['bank'],
                'customer_name' => $user->name ?? 'Customer',
                'customer_email' => $user->email ?? '',
                'customer_phone' => $user->phone ?? '',
                'item_details' => [
                    [
                        'id' => $invoice->id,
                        'price' => (int) $validated['amount'],
                        'quantity' => 1,
                        'name' => $validated['description'] ?? 'Payment for Invoice ' . $invoice->id,
                    ]
                ],
            ];

            $result = $this->midtrans->createBankTransferTransaction($params);

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => $user->id,
                'midtrans_order_id' => $result['order_id'] ?? $orderId,
                'order_id' => $result['order_id'],
                'transaction_id' => $result['transaction_id'],
                'payment_type' => 'bank_transfer',
                'amount' => $validated['amount'],
                'status' => 'pending',
                'va_numbers' => json_encode($result['va_numbers']),
                'midtrans_response' => json_encode($result['raw_response']),
                'raw_webhook_json' => $result['raw_response'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bank transfer created successfully',
                'data' => [
                    'payment_id' => $payment->id,
                    'order_id' => $result['order_id'],
                    'va_numbers' => $result['va_numbers'],
                    'amount' => $validated['amount'],
                    'status' => 'pending',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Create Bank Transfer Error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pembayaran bank transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Payment Status / Verify Payment
     * 
     * GET /api/payment/{orderId}/status
     */
    public function getPaymentStatus($orderId)
    {
        try {
            // Get from Midtrans
            $status = $this->midtrans->getTransactionStatus($orderId);

            // Update local database
            $payment = Payment::where('order_id', $orderId)->first();

            if ($payment) {
                $payment->update([
                    'status' => $this->mapMidtransStatus($status['transaction_status']),
                    'transaction_status' => $status['transaction_status'],
                    'settlement_time' => $status['settlement_time'] ?? null,
                ]);

                // Update invoice if payment success
                if (in_array($status['transaction_status'], ['settlement', 'capture'])) {
                    $invoice = $payment->invoice;
                    if ($invoice && $invoice->status !== 'paid') {
                        $invoice->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);

                        Log::info('Invoice marked as paid', [
                            'invoice_id' => $invoice->id,
                            'payment_id' => $payment->id
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $status['order_id'],
                    'transaction_status' => $status['transaction_status'],
                    'payment_type' => $status['payment_type'],
                    'gross_amount' => $status['gross_amount'],
                    'settlement_time' => $status['settlement_time'],
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get Payment Status Error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek status pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map Midtrans status to local status
     */
    private function mapMidtransStatus($midtransStatus)
    {
        $statusMap = [
            'pending' => 'pending',
            'settlement' => 'success',
            'capture' => 'success',
            'deny' => 'failed',
            'cancel' => 'cancelled',
            'expire' => 'expired',
        ];

        return $statusMap[$midtransStatus] ?? 'pending';
    }
}
