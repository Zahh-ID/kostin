<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ManualPaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ManualPaymentController extends Controller
{
    public function store(ManualPaymentRequest $request, Invoice $invoice): RedirectResponse|JsonResponse
    {
        /** @var User $tenant */
        $tenant = $request->user();

        abort_if(optional($invoice->contract)->tenant_id !== $tenant->id, 403, 'Invoice tidak ditemukan untuk akun ini.');

        if ($invoice->status === 'paid') {
            return redirect()
                ->route('tenant.invoices.show', $invoice)
                ->with('status', __('Tagihan ini sudah lunas.'));
        }

        if ($invoice->status === 'pending_verification') {
            return redirect()
                ->route('tenant.invoices.show', $invoice)
                ->with('status', __('Pembayaran manual sedang menunggu verifikasi.'));
        }

        if ($invoice->payments()->where('status', 'waiting_verification')->exists()) {
            return $this->respond($request, $invoice, [
                'status' => __('Masih terdapat pembayaran manual yang menunggu verifikasi.'),
            ]);
        }

        $method = $request->input('payment_method');
        $paymentAccount = $this->findPaymentAccount($method);

        abort_if($paymentAccount === null, 422, __('Metode pembayaran tidak tersedia.'));

        $uploadedProof = $request->file('proof');
        $storedPath = $uploadedProof->store('manual-payments', 'public');

        try {
            DB::transaction(function () use ($tenant, $invoice, $paymentAccount, $storedPath, $uploadedProof, $request): void {
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'submitted_by' => $tenant->id,
                    'user_id' => $tenant->id,
                    'order_id' => sprintf('MANUAL-%d-%s', $invoice->id, Str::orderedUuid()),
                    'payment_type' => 'manual_bank_transfer',
                    'manual_method' => $paymentAccount->method,
                    'proof_path' => $storedPath,
                    'proof_filename' => $uploadedProof->getClientOriginalName(),
                    'notes' => $request->filled('notes') ? $request->input('notes') : null,
                    'amount' => $invoice->total,
                    'status' => 'waiting_verification',
                ]);

                $invoice->update(['status' => 'pending_verification']);
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($storedPath);

            throw $exception;
        }

        return $this->respond($request, $invoice, [
            'status' => __('Bukti pembayaran berhasil dikirim. Mohon menunggu verifikasi.'),
        ]);
    }

    private function respond(ManualPaymentRequest $request, Invoice $invoice, array $data): RedirectResponse|JsonResponse
    {
        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return redirect()
            ->route('tenant.invoices.show', $invoice)
            ->with($data);
    }

    private function findPaymentAccount(string $method): ?PaymentAccount
    {
        return PaymentAccount::active()
            ->whereRaw('LOWER(method) = ?', [mb_strtolower($method)])
            ->first();
    }
}
