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
    $expiryTime = data_get($qrisPayload, 'expiry_time')
        ?? data_get($qrisPayload, 'raw_response.expiry_time')
        ?? data_get($qrisPayload, 'expires_at');
    if (! $expiryTime && $invoice->expires_at) {
        $expiryTime = $invoice->expires_at;
    }
    $transactionStatus = data_get($qrisPayload, 'transaction_status', 'pending');
    $expiryCarbon = $expiryTime ? \Illuminate\Support\Carbon::make($expiryTime) : null;
    $qrisExpired = $expiryCarbon ? now()->greaterThan($expiryCarbon) : false;
    $expiryIso = $expiryCarbon ? $expiryCarbon->timezone(config('app.timezone'))->toIso8601String() : '';
    $allowRegenerate = ! $hasQrisPayload || $qrisExpired || in_array($transactionStatus, ['expire', 'cancel', 'deny'], true);
    $reason = $invoice->status_reason;
    $statusClass = match ($invoice->status) {
        'paid' => 'success',
        'overdue' => 'danger',
        'pending_verification' => 'warning',
        'canceled' => 'secondary',
        default => 'warning',
    };
@endphp
<div class="container py-4">
    <a href="{{ route('tenant.invoices.index') }}" class="text-decoration-none small text-muted">&larr; {{ __('Kembali ke daftar tagihan') }}</a>

    @if (session('status'))
        <div class="alert alert-info mt-3">{{ session('status') }}</div>
    @endif

    <div class="card border-0 shadow-sm mt-3 mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-{{ $statusClass }} text-uppercase">{{ str_replace('_', ' ', $invoice->status) }}</span>
                    @if ($reason)
                        <span class="badge bg-light text-dark">{{ $reason }}</span>
                    @endif
                </div>
                <h1 class="h4 mb-1">{{ __('Invoice #:number', ['number' => $invoice->id]) }}</h1>
                <p class="text-muted mb-0">
                    {{ __('Periode :month/:year', ['month' => $invoice->period_month, 'year' => $invoice->period_year]) }} ·
                    {{ $invoice->contract?->room?->roomType?->property?->name }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                @if ($invoice->status === 'paid')
                    <a href="{{ route('tenant.invoices.pdf', $invoice) }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i>{{ __('Unduh PDF') }}
                    </a>
                @endif
                <form action="{{ route('tenant.invoices.pay', $invoice) }}" method="post" class="d-inline js-disable-on-submit" data-loading-text="{{ __('Mengambil data Midtrans...') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-credit-card me-1"></i>{{ __('Bayar / QRIS') }}
                    </button>
                </form>
                <a href="{{ route('tenant.invoices.index') }}" class="btn btn-light btn-sm">
                    {{ __('Kembali') }}
                </a>
                <small class="text-muted w-100 mb-0">{{ __('Klik Bayar untuk memuat QRIS terbaru dari Midtrans, lalu panel pembayaran akan muncul otomatis.') }}</small>
            </div>
        </div>
        <div class="card-body border-top d-flex flex-wrap gap-3">
            <div class="flex-fill">
                <div class="text-muted small mb-1">{{ __('Jumlah') }}</div>
                <div class="h5 mb-0">Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="flex-fill">
                <div class="text-muted small mb-1">{{ __('Jatuh Tempo') }}</div>
                <div class="fw-semibold">{{ optional($invoice->due_date)->format('d M Y') ?? '—' }}</div>
            </div>
            <div class="flex-fill">
                <div class="text-muted small mb-1">{{ __('Tambah Info') }}</div>
                <div class="fw-semibold">{{ $invoice->months_count ?? 1 }} {{ __('bulan') }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 align-items-stretch">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-3 h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Ringkasan Tagihan') }}</h5>
                    <span class="badge bg-light text-dark">{{ __('Invoice Detail') }}</span>
                </div>
                <div class="card-body">
                    <dl class="row small mb-0">
                        <dt class="col-sm-5 text-muted">{{ __('Nominal') }}</dt>
                        <dd class="col-sm-7">Rp{{ number_format($invoice->amount ?? 0, 0, ',', '.') }}</dd>
                        <dt class="col-sm-5 text-muted">{{ __('Denda') }}</dt>
                        <dd class="col-sm-7">Rp{{ number_format($invoice->late_fee ?? 0, 0, ',', '.') }}</dd>
                        <dt class="col-sm-5 text-muted">{{ __('Total') }}</dt>
                        <dd class="col-sm-7 fw-semibold">Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</dd>
                        <dt class="col-sm-5 text-muted">{{ __('Status') }}</dt>
                        <dd class="col-sm-7 text-capitalize">{{ str_replace('_', ' ', $invoice->status) }}</dd>
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
                        @if ($reason)
                            <dt class="col-sm-5 text-muted">{{ __('Catatan Status') }}</dt>
                            <dd class="col-sm-7">{{ $reason }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Informasi Properti') }}</h5>
                    @if ($invoice->contract)
                        <a href="{{ route('tenant.contracts.show', $invoice->contract) }}" class="small link-primary">{{ __('Lihat Kontrak') }}</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="fw-semibold mb-1">{{ $invoice->contract?->room?->roomType?->property?->name ?? '—' }}</div>
                    <div class="text-muted small mb-2">{{ __('Kamar :code', ['code' => $invoice->contract?->room?->room_code ?? '—']) }}</div>
                    @if ($invoice->contract?->room?->roomType)
                        <div class="text-muted small mb-2">{{ $invoice->contract?->room?->roomType?->name }}</div>
                    @endif
                    <div class="text-muted small">{{ $invoice->contract?->room?->roomType?->property?->address ?? '' }}</div>
                </div>
            </div>
        </div>
    </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Riwayat Pembayaran') }}</h5>
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

<div class="modal fade" id="qrisModal" tabindex="-1" aria-labelledby="qrisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-body position-relative">
                <button type="button" class="btn-close position-absolute end-0 top-0 mt-2 me-2" data-bs-dismiss="modal" aria-label="{{ __('Tutup') }}"></button>
                <div class="mb-3">
                    <h6 class="mb-1" id="qrisModalLabel">{{ __('Pembayaran QRIS') }}</h6>
                    <p class="text-muted small mb-0">{{ __('Generate, scan, atau cek status Midtrans tanpa meninggalkan halaman.') }}</p>
                </div>

                @if ($invoice->status === 'paid')
                    <div class="alert alert-success mb-0">{{ __('Tagihan ini sudah lunas. Tidak perlu pembayaran baru.') }}</div>
                @elseif(in_array($invoice->status, ['expired','canceled']))
                    <div class="alert alert-warning mb-0">{{ __('QRIS tidak tersedia karena status tagihan sudah :status.', ['status' => $invoice->status]) }}</div>
                @else
                    @if ($invoice->expires_at && now()->greaterThan($invoice->expires_at))
                        <div class="alert alert-warning small">{{ __('QRIS sebelumnya kedaluwarsa. Generate ulang untuk melanjutkan pembayaran.') }}</div>
                    @endif
                    @if ($hasQrisPayload && in_array($transactionStatus, ['expire', 'cancel', 'deny'], true))
                        <div class="alert alert-warning small">{{ __('Status Midtrans terakhir: :status. Silakan generate ulang lalu scan.', ['status' => str_replace('_', ' ', $transactionStatus)]) }}</div>
                    @endif

                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">{{ __('Jumlah Tagihan') }}</div>
                                <div class="small text-muted">{{ $invoice->contract?->room?->roomType?->property?->name ?? '—' }} · {{ $invoice->contract?->room?->room_code ?? '—' }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</div>
                                <span class="badge bg-{{ $qrisExpired ? 'warning text-dark' : 'info text-dark' }}">{{ $qrisExpired ? __('Kedaluwarsa') : __('Aktif') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        @if ($allowRegenerate)
                            <form
                                action="{{ route('tenant.invoices.pay', $invoice) }}"
                                method="post"
                                class="js-disable-on-submit js-regenerate-form"
                                data-loading-text="{{ __('Memproses...') }}"
                                @if($expiryIso) data-auto-regenerate="true" data-qris-expiry="{{ $expiryIso }}" @endif
                                @if(in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) data-auto-regenerate-status="true" data-qris-status="{{ $transactionStatus }}" @endif
                            >
                                @csrf
                                <button
                                    class="btn btn-primary w-100 js-regenerate-btn"
                                    type="submit"
                                    data-active-text="{{ __('Generate Ulang QRIS') }}"
                                    data-wait-text="{{ __('QRIS masih aktif. Menunggu kedaluwarsa...') }}"
                                    @disabled($invoice->status === 'pending_verification')
                                >
                                    {{ $hasQrisPayload ? __('Generate Ulang QRIS') : __('Generate & Lihat QRIS') }}
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('tenant.invoices.check-status', $invoice) }}" method="post" class="js-disable-on-submit" data-loading-text="{{ __('Memproses...') }}">
                            @csrf
                            <button class="btn btn-outline-primary w-100" type="submit">
                                {{ __('Cek Status Pembayaran') }}
                            </button>
                        </form>
                    </div>

                    @if ($expiryIso && ! $allowRegenerate)
                        <form action="{{ route('tenant.invoices.pay', $invoice) }}" method="post" class="d-none js-auto-regenerate" data-qris-expiry="{{ $expiryIso }}">
                            @csrf
                        </form>
                    @endif

                    @if ($hasQrisPayload)
                        <div class="border rounded-3 p-3 bg-light mb-3" @if($expiryIso) data-qris-expiry="{{ $expiryIso }}" @endif>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>{{ __('QRIS terbaru') }}</strong>
                                <span class="badge bg-{{ $qrisExpired ? 'warning text-dark' : 'info text-dark' }}">
                                    {{ $qrisExpired ? __('Kedaluwarsa') : __('Aktif') }}
                                </span>
                            </div>
                            <p class="text-muted small mb-3 mb-lg-2">{{ __('Scan kode di bawah atau generate ulang jika sudah kedaluwarsa.') }}</p>
                            @if ($qrImageUrl || $qrString)
                                <div class="text-center mb-3">
                                    @if ($qrImageUrl)
                                        <img src="{{ $qrImageUrl }}" alt="{{ __('Kode QRIS Midtrans') }}" class="img-fluid mx-auto d-block" style="max-width: 260px;">
                                    @elseif ($qrString)
                                        <pre class="bg-white border rounded p-2 small text-wrap">{{ $qrString }}</pre>
                                    @endif
                                </div>
                            @endif
                            <dl class="row small text-muted mb-0">
                                <dt class="col-5">{{ __('Order ID') }}</dt>
                                <dd class="col-7">{{ data_get($qrisPayload, 'order_id', $invoice->external_order_id ?? '—') }}</dd>
                                @php
                                    $merchantName = data_get($qrisPayload, 'merchant_name') ?? data_get($qrisPayload, 'raw_response.merchant_name');
                                    $grossAmount = data_get($qrisPayload, 'gross_amount') ?? data_get($qrisPayload, 'raw_response.transaction_details.gross_amount') ?? $invoice->total;
                                @endphp
                                @if ($merchantName)
                                    <dt class="col-5">{{ __('Merchant') }}</dt>
                                    <dd class="col-7">{{ $merchantName }}</dd>
                                @endif
                                <dt class="col-5">{{ __('Status Midtrans') }}</dt>
                                <dd class="col-7 text-capitalize">{{ str_replace('_', ' ', $transactionStatus) }}</dd>
                                <dt class="col-5">{{ __('Nominal') }}</dt>
                                <dd class="col-7">Rp{{ number_format((int) $grossAmount, 0, ',', '.') }}</dd>
                                <dt class="col-5">{{ __('Properti') }}</dt>
                                <dd class="col-7">{{ $invoice->contract?->room?->roomType?->property?->name ?? '—' }}</dd>
                            </dl>
                            <hr class="my-3">
                            <div class="small text-muted">
                                <div><strong>{{ __('Properti') }}:</strong> {{ $invoice->contract?->room?->roomType?->property?->name ?? '—' }}</div>
                                <div><strong>{{ __('Kamar') }}:</strong> {{ $invoice->contract?->room?->room_code ?? '—' }}</div>
                            </div>
                        </div>
                    @endif
                @endif

                <div class="border-top pt-3">
                    <h6 class="mb-2">{{ __('Transfer Manual') }}</h6>
                    @if ($invoice->status === 'paid')
                        <div class="alert alert-success mb-0">{{ __('Tagihan ini telah lunas. Tidak perlu pembayaran baru.') }}</div>
                    @elseif ($paymentAccounts->isEmpty())
                        <div class="alert alert-warning mb-0">{{ __('Pemilik belum menambahkan informasi rekening manual. Silakan hubungi pemilik untuk detail pembayaran.') }}</div>
                    @else
                        @if ($invoice->status === 'pending_verification')
                            <div class="alert alert-warning">{{ __('Bukti pembayaran Anda sedang divalidasi oleh pemilik/admin.') }}</div>
                        @endif
                        <p class="text-muted small">{{ __('Pilih metode transfer, unggah bukti pembayaran, dan sertakan catatan jika diperlukan.') }}</p>
                        <form action="{{ route('tenant.invoices.manual-payment.store', $invoice) }}" method="post" enctype="multipart/form-data" class="js-disable-on-submit" data-loading-text="{{ __('Mengunggah...') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">{{ __('Metode') }}</label>
                                <select name="payment_method" id="payment_method" class="form-select" @if($invoice->status === 'pending_verification') disabled @endif>
                                    <option value="" disabled selected>{{ __('Pilih bank atau metode pembayaran') }}</option>
                                    @foreach ($paymentAccounts as $account)
                                        <option value="{{ $account->method }}" @selected(old('payment_method') === $account->method)>
                                            {{ $account->method }} @if($account->account_number) · {{ $account->account_number }} ({{ $account->account_name }}) @endif
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
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const shouldShowModal = @json(session('show_qris_modal', false));
            if (shouldShowModal) {
                const modalElement = document.getElementById('qrisModal');
                if (modalElement && window.bootstrap?.Modal) {
                    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalInstance.show();
                }
            }

            document.querySelectorAll('.js-disable-on-submit').forEach((form) => {
                form.addEventListener('submit', () => {
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (!submitButton) {
                        return;
                    }

                    submitButton.disabled = true;
                    const loadingText = form.dataset.loadingText || submitButton.dataset.loadingText || '{{ __('Memproses...') }}';
                    submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${loadingText}`;
                });
            });

            const regenForms = document.querySelectorAll('.js-regenerate-form');

            regenForms.forEach((formElement) => {
                if (formElement.dataset.regenTriggered === 'true') {
                    return;
                }

                const submitForm = (form) => {
                    form.dataset.regenTriggered = 'true';
                    if (form.requestSubmit) {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                };

                const status = formElement.dataset.qrisStatus ?? '';
                if (formElement.dataset.autoRegenerateStatus === 'true' && ['expire', 'cancel', 'deny'].includes(status)) {
                    submitForm(formElement);
                    return;
                }

                const expiryIso = formElement.dataset.qrisExpiry;
                if (expiryIso) {
                    const expiry = new Date(expiryIso);
                    const delay = expiry.getTime() - Date.now();
                    if (delay <= 0) {
                        submitForm(formElement);
                        return;
                    }
                    setTimeout(() => submitForm(formElement), delay + 300);
                }
            });
        });
    </script>
@endpush
