<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class InvoicePaymentController extends Controller
{
    public function __construct(private readonly MidtransService $midtransService) {}

    /**
     * Initiate QRIS payment for an invoice.
     */
    public function __invoke(Request $request, Invoice $invoice)
    {
        $tenant = $request->user();
        abort_if(optional($invoice->contract)->tenant_id !== $tenant->id, 403, 'Invoice tidak ditemukan.');

        if ($invoice->status === 'paid') {
            return $this->respond($request, $invoice, [
                'message' => 'Invoice sudah lunas.',
                'status' => 'paid',
            ], 422);
        }

        if ($this->hasActiveQrisPayload($invoice)) {
            return $this->respond($request, $invoice, [
                'message' => 'QRIS masih aktif.',
                'status' => 'pending',
                'payload' => $invoice->qris_payload,
                'show_qris_modal' => true,
            ]);
        }

        $invoice->loadMissing('contract.tenant');

        $orderId = sprintf('INV-%d-%s', $invoice->id, Str::orderedUuid());
        $amount = $invoice->total;

        try {
            $payload = $this->midtransService->buildQrisPayload(
                $orderId,
                sprintf('Invoice #%d', $invoice->id),
                $amount,
                $tenant->name,
                $tenant->email
            );

            $response = $this->midtransService->chargeQris($payload);
        } catch (RuntimeException $exception) {
            return $this->respond($request, [
                'message' => $exception->getMessage(),
                'status' => 'failed',
            ], 422);
        }

        DB::transaction(function () use ($invoice, $response, $amount, $tenant, $orderId): void {
            $payment = $invoice->payments()
                ->where('payment_type', 'qris')
                ->where('status', 'pending')
                ->latest()
                ->first();

            $payloadAmount = $this->normalizeGrossAmount($response['gross_amount'] ?? $amount);

            if ($payment) {
                $payment->update([
                    'submitted_by' => $tenant->id,
                    'user_id' => $tenant->id,
                    'midtrans_order_id' => $response['order_id'] ?? $orderId,
                    'order_id' => $response['order_id'] ?? $orderId,
                    'amount' => $payloadAmount,
                    'status' => 'pending',
                    'raw_webhook_json' => $response,
                    'paid_at' => null,
                ]);
            } else {
                $invoice->payments()->create([
                    'submitted_by' => $tenant->id,
                    'user_id' => $tenant->id,
                    'midtrans_order_id' => $response['order_id'] ?? $orderId,
                    'order_id' => $response['order_id'] ?? $orderId,
                    'payment_type' => 'qris',
                    'amount' => $payloadAmount,
                    'status' => 'pending',
                    'raw_webhook_json' => $response,
                ]);
            }

            $invoice->update([
                'external_order_id' => $response['order_id'] ?? $orderId,
                'qris_payload' => $response,
                'status' => $invoice->status === 'overdue' ? 'overdue' : 'unpaid',
            ]);
        });

        $this->recordAudit('payment_initiated', 'invoice', $invoice->id, [
            'order_id' => $response['order_id'] ?? $orderId,
        ]);

        return $this->respond($request, $invoice, [
            'message' => 'QRIS siap digunakan.',
            'status' => 'pending',
            'payload' => $response,
            'show_qris_modal' => true,
        ]);
    }

    private function respond(Request $request, Invoice $invoice, array $data, int $status = 200)
    {
        if ($request->wantsJson()) {
            return response()->json($data, $status);
        }

        $flash = [
            'status' => $data['message'] ?? 'Permintaan diproses.',
        ];

        if (! empty($data['show_qris_modal'])) {
            $flash['show_qris_modal'] = true;
        }

        return redirect()
            ->route('tenant.invoices.show', $invoice)
            ->with($flash);
    }

    private function hasActiveQrisPayload(Invoice $invoice): bool
    {
        if (! is_array($invoice->qris_payload)) {
            return false;
        }

        $expiry = Arr::get($invoice->qris_payload, 'expiry_time')
            ?? Arr::get($invoice->qris_payload, 'expires_at');

        if ($expiry === null) {
            return false;
        }

        try {
            $expiresAt = Carbon::parse($expiry);
        } catch (\Exception) {
            return false;
        }

        if ($expiresAt->isPast()) {
            return false;
        }

        $grossAmount = Arr::get($invoice->qris_payload, 'gross_amount');

        return (int) $this->normalizeGrossAmount($grossAmount ?? $invoice->total) === (int) $invoice->total;
    }

    private function normalizeGrossAmount(int|string|null $amount): int
    {
        if ($amount === null) {
            return 0;
        }

        if (is_int($amount)) {
            return $amount;
        }

        return (int) round((float) $amount);
    }
}
