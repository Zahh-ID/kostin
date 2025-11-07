<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 text-dark mb-0">{{ __('Simulasi Webhook Midtrans') }}</h1>
            <p class="text-muted small mb-0">{{ __('Gunakan halaman ini untuk memperbarui status pembayaran QRIS secara manual di lingkungan sandbox.') }}</p>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h2 class="h5 mb-0">{{ __('Kirim Webhook Simulasi') }}</h2>
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
                <h2 class="h5 mb-0">{{ __('Pembayaran QRIS Terbaru') }}</h2>
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
</x-app-layout>
