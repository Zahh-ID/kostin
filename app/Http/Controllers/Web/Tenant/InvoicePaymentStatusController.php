<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class InvoicePaymentStatusController extends Controller
{
    public function __construct(private readonly MidtransService $midtransService) {}

    public function __invoke(Request $request, Invoice $invoice): RedirectResponse|JsonResponse
    {
        $tenant = $request->user();
        abort_if(optional($invoice->contract)->tenant_id !== $tenant->id, 403, 'Invoice tidak ditemukan.');

        $payment = $invoice->payments()
            ->whereNotNull('midtrans_order_id')
            ->latest()
            ->first();

        if ($payment === null) {
            return $this->respond($request, $invoice, [
                'status' => __('Belum ada pembayaran QRIS yang dapat dicek.'),
                'payload' => $invoice->qris_payload,
                'show_qris_modal' => true,
            ]);
        }

        try {
            $status = $this->midtransService->getTransactionStatus($payment->midtrans_order_id);
        } catch (\Throwable $throwable) {
            return $this->respond($request, $invoice, [
                'status' => $throwable->getMessage(),
                'payload' => $invoice->qris_payload,
                'show_qris_modal' => true,
            ]);
        }

        $mappedStatus = $this->mapMidtransStatus($status['transaction_status'] ?? null);

        $payment->update([
            'status' => $mappedStatus,
            'paid_at' => $this->shouldMarkPaid($status['transaction_status'] ?? null) ? now() : $payment->paid_at,
            'raw_webhook_json' => $status['raw_response'] ?? $status,
        ]);

        if ($mappedStatus === 'success') {
            $invoice->update([
                'status' => 'paid',
                'qris_payload' => $status['raw_response'] ?? $invoice->qris_payload,
            ]);
        } elseif (in_array($status['transaction_status'], ['expire', 'cancel'], true)) {
            $invoice->update(['status' => 'overdue']);
        }

        return $this->respond($request, $invoice, [
            'status' => __('Status transaksi diperbarui menjadi :status', ['status' => $mappedStatus]),
            'show_qris_modal' => true,
            'payload' => $status['raw_response'] ?? $status ?? $invoice->qris_payload,
        ]);
    }

    private function mapMidtransStatus(?string $status): string
    {
        return match ($status) {
            'capture', 'settlement' => 'success',
            'pending' => 'pending',
            'expire', 'cancel', 'deny' => 'failed',
            default => 'pending',
        };
    }

    private function shouldMarkPaid(?string $status): bool
    {
        return in_array($status, ['capture', 'settlement'], true);
    }

    private function respond(Request $request, Invoice $invoice, array $data): RedirectResponse|JsonResponse
    {
        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return redirect()
            ->route('tenant.invoices.show', $invoice)
            ->with($data);
    }
}
