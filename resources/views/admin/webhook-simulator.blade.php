<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} – Webhook Midtrans</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body { background: #f7f8fb; }
        .page { max-width: 1100px; margin: 28px auto; padding: 0 16px; }
        .card { border-radius: 14px; }
    </style>
</head>
<body>
<div class="page">
    <header class="d-flex justify-content-between align-items-start mb-3 gap-2 flex-wrap">
        <div>
            <h1 class="h4 mb-1">{{ __('Simulasi Webhook Midtrans') }}</h1>
            <p class="text-muted small mb-0">{{ __('Perbarui status pembayaran QRIS secara manual.') }}</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">{{ __('Kembali ke Dashboard') }}</a>
    </header>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white border-0">
            <h2 class="h6 mb-0">{{ __('Kirim Webhook Simulasi') }}</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.webhook.midtrans.simulate') }}" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label for="payment_id" class="form-label">{{ __('Pilih Pembayaran') }}</label>
                    <select id="payment_id" name="payment_id" class="form-select" required>
                        <option value="">{{ __('Pilih salah satu pembayaran QRIS pending') }}</option>
                        @foreach ($payments as $payment)
                            <option value="{{ $payment->id }}" @selected(old('payment_id') == $payment->id)>
                                #{{ $payment->id }} · {{ $payment->invoice?->contract?->tenant?->name ?? __('Tenant') }} · Rp{{ number_format($payment->amount ?? 0, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="transaction_status" class="form-label">{{ __('Status Transaksi') }}</label>
                    <select id="transaction_status" name="transaction_status" class="form-select" required>
                        <option value="settlement" @selected(old('transaction_status') === 'settlement')>{{ __('settlement (success)') }}</option>
                        <option value="capture" @selected(old('transaction_status') === 'capture')>{{ __('capture (success)') }}</option>
                        <option value="pending" @selected(old('transaction_status') === 'pending')>{{ __('pending') }}</option>
                        <option value="expire" @selected(old('transaction_status') === 'expire')>{{ __('expire') }}</option>
                        <option value="cancel" @selected(old('transaction_status') === 'cancel')>{{ __('cancel') }}</option>
                        <option value="deny" @selected(old('transaction_status') === 'deny')>{{ __('deny') }}</option>
                    </select>
                    @error('transaction_status')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Kirim Webhook') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">{{ __('Pembayaran QRIS Terbaru') }}</h2>
            <span class="text-muted small">{{ __('Menampilkan 10 entri terbaru') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>{{ __('Pembayaran') }}</th>
                        <th>{{ __('Order ID') }}</th>
                        <th>{{ __('Tenant') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Nominal') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>#{{ $payment->id }}</td>
                            <td class="text-muted small">{{ $payment->midtrans_order_id ?? $payment->order_id }}</td>
                            <td class="text-muted small">{{ $payment->invoice?->contract?->tenant?->name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $payment->status === 'success' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ $payment->status }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">Rp{{ number_format($payment->amount ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">{{ __('Belum ada pembayaran QRIS.') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($payments instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="card-footer bg-white border-0">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
