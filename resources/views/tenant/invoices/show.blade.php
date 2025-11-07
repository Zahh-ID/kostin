@extends('layouts.app')

@section('content')
@php
    $qrisPayload = $invoice->qris_payload ?? [];
    $hasQrisPayload = is_array($qrisPayload) && ! empty($qrisPayload);
    $qrActions = collect(data_get($qrisPayload, 'actions', []));
    if ($qrActions->isEmpty()) {
        $qrActions = collect(data_get($qrisPayload, 'raw_response.actions', []));
    }
    $qrString = data_get($qrisPayload, 'qr_string')
        ?? data_get($qrisPayload, 'raw_response.qr_string');
    $qrImageUrl = data_get($qrisPayload, 'qr_image_url')
        ?? data_get($qrisPayload, 'qris_string')
        ?? $qrActions->firstWhere('name', 'generate-qr-code')['url']
        ?? $qrActions->first()['url']
        ?? null;
    if (! $qrImageUrl && $qrString) {
        $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&data='
            . urlencode($qrString);
    }
    $expiryTime = data_get($qrisPayload, 'expiry_time') ?? data_get($qrisPayload, 'expires_at');
    $transactionStatus = data_get($qrisPayload, 'transaction_status', 'pending');
    $expiryCarbon = $expiryTime ? \Illuminate\Support\Carbon::make($expiryTime) : null;
@endphp
<div class="container py-4">
    <a href="{{ route('tenant.invoices.index') }}" class="text-decoration-none small text-muted">&larr; {{ __('Kembali ke daftar tagihan') }}</a>

    @if (session('status'))
        <div class="alert alert-info mt-3">{{ session('status') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Invoice #:number', ['number' => $invoice->id]) }}</h1>
            <p class="text-muted mb-0">
                {{ __('Periode :month/:year', ['month' => $invoice->period_month, 'year' => $invoice->period_year]) }} &middot;
                {{ $invoice->contract?->room?->roomType?->property?->name }}
            </p>
        </div>
        @php
            $statusClass = match ($invoice->status) {
                'paid' => 'success',
                'overdue' => 'danger',
                'pending_verification' => 'warning',
                default => 'warning',
            };
        @endphp
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('tenant.invoices.pdf', $invoice) }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
                {{ __('Unduh PDF') }}
            </a>
            <span class="badge bg-{{ $statusClass }} text-uppercase">{{ str_replace('_', ' ', $invoice->status) }}</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h2 class="h6 fw-semibold mb-0">{{ __('Rincian Tagihan') }}</h2>
                </div>
                <div class="card-body">
                    <dl class="row small mb-0">
                        <dt class="col-sm-5 text-muted">{{ __('Jatuh Tempo') }}</dt>
                        <dd class="col-sm-7">{{ optional($invoice->due_date)->format('d M Y') ?? '—' }}</dd>
                        <dt class="col-sm-5 text-muted">{{ __('Nominal') }}</dt>
                        <dd class="col-sm-7">Rp{{ number_format($invoice->amount ?? 0, 0, ',', '.') }}</dd>
                        <dt class="col-sm-5 text-muted">{{ __('Denda') }}</dt>
                        <dd class="col-sm-7">Rp{{ number_format($invoice->late_fee ?? 0, 0, ',', '.') }}</dd>
                        <dt class="col-sm-5 text-muted">{{ __('Total') }}</dt>
                        <dd class="col-sm-7 fw-semibold">Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</dd>
                        <dt class="col-sm-5 text-muted">{{ __('Periode Dicakup') }}</dt>
                        <dd class="col-sm-7">
                            {{ $invoice->months_count ?? 1 }} {{ __('bulan') }}
                            @if ($invoice->coverage_start_month && $invoice->coverage_end_month)
                                <div class="text-muted">
                                    {{ \Illuminate\Support\Carbon::create($invoice->coverage_start_year, $invoice->coverage_start_month, 1)->translatedFormat('M Y') }}
                                    -
                                    {{ \Illuminate\Support\Carbon::create($invoice->coverage_end_year, $invoice->coverage_end_month, 1)->translatedFormat('M Y') }}
                                </div>
                            @endif
                        </dd>
                        <dt class="col-sm-5 text-muted">{{ __('Status') }}</dt>
                        <dd class="col-sm-7 text-capitalize">{{ str_replace('_', ' ', $invoice->status) }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h2 class="h6 fw-semibold mb-0">{{ __('Riwayat Pembayaran') }}</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>{{ __('Tanggal') }}</th>
                                <th>{{ __('Channel') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Jumlah') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($invoice->payments as $payment)
                                @php
                                    $channel = $payment->payment_type === 'manual_bank_transfer'
                                        ? ($payment->manual_method ?? 'Manual Transfer')
                                        : strtoupper($payment->payment_type);
                                @endphp
                                <tr>
                                    <td>{{ optional($payment->created_at)->format('d M Y H:i') }}</td>
                                    <td>{{ $channel }}</td>
                                    <td>{{ ucfirst($payment->status ?? 'success') }}</td>
                                    <td class="text-end">Rp{{ number_format($payment->amount ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">{{ __('Belum ada pembayaran tercatat.') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" id="paymentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="qris-tab" data-bs-toggle="pill" data-bs-target="#qris-pane" type="button" role="tab" aria-controls="qris-pane" aria-selected="true">
                                {{ __('QRIS') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="manual-tab" data-bs-toggle="pill" data-bs-target="#manual-pane" type="button" role="tab" aria-controls="manual-pane" aria-selected="false">
                                {{ __('Transfer Manual') }}
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="paymentTabsContent">
                        <div class="tab-pane fade show active" id="qris-pane" role="tabpanel" aria-labelledby="qris-tab">
                            @if ($invoice->status === 'paid')
                                <div class="alert alert-success mb-0">{{ __('Tagihan ini telah lunas. Terima kasih!') }}</div>
                            @else
                                <p class="text-muted small">{{ __('Klik tombol berikut untuk mendapatkan kode QRIS resmi Midtrans dan melakukan pembayaran.') }}</p>
                                <form action="{{ route('tenant.invoices.pay', $invoice) }}" method="post" class="js-qris-generate-form">
                                    @csrf
                                    <button class="btn btn-primary w-100" type="submit" data-loading-text="{{ __('Memproses...') }}">
                                        {{ __('Bayar via QRIS') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="manual-pane" role="tabpanel" aria-labelledby="manual-tab">
                            @if ($invoice->status === 'paid')
                                <div class="alert alert-success mb-0">{{ __('Tagihan ini telah lunas. Terima kasih!') }}</div>
                            @elseif ($paymentAccounts->isEmpty())
                                <div class="alert alert-warning mb-0">{{ __('Pemilik belum menambahkan informasi rekening manual. Silakan hubungi pemilik untuk detail pembayaran.') }}</div>
                            @else
                                @if ($invoice->status === 'pending_verification')
                                    <div class="alert alert-warning">{{ __('Bukti pembayaran Anda sedang divalidasi oleh pemilik/admin.') }}</div>
                                @endif
                                <p class="text-muted small">{{ __('Pilih metode transfer, unggah bukti pembayaran, dan sertakan catatan jika diperlukan.') }}</p>
                                <form action="{{ route('tenant.invoices.manual-payment.store', $invoice) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label">{{ __('Metode') }}</label>
                                        <select name="payment_method" id="payment_method" class="form-select" @if($invoice->status === 'pending_verification') disabled @endif>
                                            <option value="" disabled selected>{{ __('Pilih bank atau metode pembayaran') }}</option>
                                            @foreach ($paymentAccounts as $account)
                                                <option value="{{ $account->method }}" @selected(old('payment_method') === $account->method)>
                                                    {{ $account->method }} @if($account->account_number) &middot; {{ $account->account_number }} ({{ $account->account_name }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('payment_method')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="proof" class="form-label">{{ __('Bukti Pembayaran (jpg/png, maks 5MB)') }}</label>
                                        <input type="file" name="proof" id="proof" class="form-control" accept="image/jpeg,image/png" @if($invoice->status === 'pending_verification') disabled @endif>
                                        @error('proof')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">{{ __('Catatan (opsional)') }}</label>
                                        <textarea name="notes" id="notes" rows="3" class="form-control" @if($invoice->status === 'pending_verification') disabled @endif>{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary w-100" @if($invoice->status === 'pending_verification') disabled @endif>
                                        {{ __('Kirim Bukti Pembayaran') }}
                                    </button>
                                </form>

                                <div class="mt-3">
                                    @foreach ($paymentAccounts as $account)
                                        <div class="border rounded-3 p-3 mb-2">
                                            <p class="fw-semibold mb-1">{{ $account->method }}</p>
                                            @if ($account->account_number)
                                                <p class="text-muted small mb-1">{{ $account->account_number }} ({{ $account->account_name }})</p>
                                            @endif
                                            @if ($account->instructions)
                                                <p class="text-muted small mb-0">{{ $account->instructions }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 fw-semibold mb-3">{{ __('Informasi Kontrak') }}</h2>
                    <p class="mb-1 fw-semibold">{{ $invoice->contract?->room?->roomType?->property?->name ?? '—' }}</p>
                    <p class="text-muted small mb-2">{{ __('Kamar :code', ['code' => $invoice->contract?->room?->room_code ?? '—']) }}</p>
                    @if ($invoice->contract)
                        <a href="{{ route('tenant.contracts.show', $invoice->contract) }}" class="btn btn-outline-primary btn-sm">{{ __('Lihat Kontrak') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if ($hasQrisPayload)
    <div class="modal fade" id="qrisPaymentModal" tabindex="-1" aria-labelledby="qrisPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrisPaymentModalLabel">{{ __('Pembayaran QRIS') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Tutup') }}"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        {{ __('Tunjukkan atau pindai kode berikut menggunakan aplikasi bank atau e-wallet Anda. Pastikan nominal pembayaran sesuai dengan tagihan.') }}
                    </p>
                    <div class="border rounded p-3 text-center bg-light">
                        @if ($qrImageUrl)
                            <img src="{{ $qrImageUrl }}" alt="{{ __('Kode QRIS Midtrans') }}" class="img-fluid mx-auto d-block" style="max-width: 260px;">
                        @elseif ($qrString)
                            <pre class="bg-white border rounded p-2 small text-wrap">{{ $qrString }}</pre>
                        @else
                            <span class="text-muted small">{{ __('Payload QRIS belum lengkap. Mohon refresh kode pembayaran.') }}</span>
                        @endif
                    </div>
                    <dl class="row small text-muted mt-3 mb-0">
                        <dt class="col-5">{{ __('Order ID') }}</dt>
                        <dd class="col-7">{{ data_get($qrisPayload, 'order_id', $invoice->external_order_id ?? '—') }}</dd>
                        <dt class="col-5">{{ __('Status') }}</dt>
                        <dd class="col-7 text-capitalize">{{ str_replace('_', ' ', $transactionStatus) }}</dd>
                        @if ($expiryCarbon)
                            <dt class="col-5">{{ __('Berlaku hingga') }}</dt>
                            <dd class="col-7">{{ $expiryCarbon->timezone(config('app.timezone'))->format('d M Y H:i') }}</dd>
                        @endif
                    </dl>
                </div>
                <div class="modal-footer d-flex justify-content-between flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Tutup') }}</button>
                    <form action="{{ route('tenant.invoices.check-status', $invoice) }}" method="post">
                        @csrf
                        <button class="btn btn-primary" type="submit">{{ __('Cek Status Pembayaran') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const shouldShowModal = @json(session('show_qris_modal', false));
            if (shouldShowModal) {
                const modalElement = document.getElementById('qrisPaymentModal');
                if (modalElement) {
                    const showModal = () => {
                        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
                        modalInstance.show();
                    };

                    if (window.bootstrap && window.bootstrap.Modal) {
                        showModal();
                    } else {
                        const observer = new MutationObserver(() => {
                            if (window.bootstrap && window.bootstrap.Modal) {
                                observer.disconnect();
                                showModal();
                            }
                        });
                        observer.observe(window.document.body, { childList: true, subtree: true });
                        setTimeout(() => observer.disconnect(), 5000);
                    }
                }
            }

            document.querySelectorAll('.js-qris-generate-form').forEach((form) => {
                form.addEventListener('submit', () => {
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (!submitButton) {
                        return;
                    }

                    submitButton.disabled = true;
                    const loadingText = submitButton.dataset.loadingText || '{{ __('Memproses...') }}';
                    submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${loadingText}`;
                });
            });
        });
    </script>
@endpush
