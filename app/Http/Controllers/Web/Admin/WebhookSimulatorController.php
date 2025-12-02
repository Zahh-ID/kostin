<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WebhookController;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class WebhookSimulatorController extends Controller
{
    public function __construct(private readonly WebhookController $webhookController)
    {
    }

    public function index(Request $request): View
    {
        $payments = Payment::query()
            ->with(['invoice.contract.tenant'])
            ->whereNotNull('order_id')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.webhook-simulator', [
            'payments' => $payments,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_id' => ['required', 'exists:payments,id'],
            'transaction_status' => ['required', 'in:settlement,capture,pending,expire,cancel,deny'],
        ]);

        $payment = Payment::with('invoice')->findOrFail($validated['payment_id']);
        $orderId = $payment->midtrans_order_id ?? $payment->order_id;
        $grossAmount = (string) ((int) $payment->amount);

        $statusCode = match ($validated['transaction_status']) {
            'settlement', 'capture' => '200',
            'pending' => '201',
            default => '202',
        };

        $payload = [
            'transaction_status' => $validated['transaction_status'],
            'fraud_status' => $validated['transaction_status'] === 'settlement' ? 'accept' : 'deny',
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
        ];

        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('midtrans.server_key')
        );
        $payload['force_success'] = true;

        $webhookRequest = Request::create(
            '/webhook/midtrans',
            'POST',
            $payload
        );

        $this->webhookController->handleNotification($webhookRequest);

        return redirect()
            ->route('admin.webhook.midtrans.form')
            ->with('status', __('Webhook Midtrans simulasi berhasil dikirim.'));
    }
}
