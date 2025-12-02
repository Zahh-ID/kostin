<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div>
                <p class="badge bg-primary-subtle text-primary-emphasis text-uppercase mb-1">{{ __(auth()->user()->role ?? 'User') }}</p>
                <h1 class="h4 mb-0 text-dark">{{ __('Halo, :name', ['name' => auth()->user()->name]) }}</h1>
                <small class="text-muted">{{ __('Ringkasan cepat: pembayaran, tiket, moderasi, dan properti sesuai peran.') }}</small>
            </div>
            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('tenant.invoices.index') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-receipt me-1"></i>{{ __('Tagihan') }}
                </a>
                <a href="{{ route('chat.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-chat-dots me-1"></i>{{ __('Chat') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="container-fluid">
        @if ($cards->isNotEmpty())
            <div class="row g-3 mb-4">
                @foreach ($cards as $card)
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-uppercase text-muted small mb-1">{{ $card['label'] }}</p>
                                <h3 class="mb-1">{{ $card['value'] }}</h3>
                                <p class="text-muted small mb-0">{{ $card['description'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info border-0 shadow-sm mb-4">
                {{ __('Tidak ada ringkasan tambahan untuk peran Anda. Gunakan menu navigasi untuk mulai bekerja.') }}
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-12 col-xl-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title d-flex align-items-center gap-2">
                            <i class="bi bi-lightning-charge-fill text-primary"></i>{{ __('Aksi Prioritas') }}
                        </h5>
                        <p class="text-muted mb-3">{{ __('Lompat langsung ke pekerjaan yang paling sering Anda lakukan.') }}</p>
                        @if ($actions->isNotEmpty())
                            <div class="row g-2">
                                @foreach ($actions as $action)
                                    <div class="col-12 col-md-6">
                                        <a href="{{ isset($action['route']) && \Illuminate\Support\Facades\Route::has($action['route']) ? route($action['route']) : '#' }}" class="btn btn-outline-primary w-100 text-start d-flex flex-column align-items-start @if (! isset($action['route']) || ! \Illuminate\Support\Facades\Route::has($action['route'])) disabled @endif">
                                            <span class="fw-semibold">{{ $action['label'] }}</span>
                                            <small class="text-muted">{{ $action['description'] ?? '' }}</small>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small mb-0">{{ __('Semua fitur utama tersedia melalui navigasi sisi kiri.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted mb-3">{{ __('Tips cepat') }}</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2">
                                <strong>{{ __('Tenant') }}:</strong> {{ __('Cek tagihan, unduh PDF, bayar via QRIS atau unggah manual, dan buka tiket jika ada kendala.') }}
                            </li>
                            <li class="mb-2">
                                <strong>{{ __('Owner') }}:</strong> {{ __('Tambah/ubah properti, pantau okupansi, dan verifikasi pembayaran manual tenant.') }}
                            </li>
                            <li>
                                <strong>{{ __('Admin') }}:</strong> {{ __('Moderasi properti menunggu approval dan pantau tiket support rentang SLA.') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted mb-2">{{ __('Navigasi Cepat') }}</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('tenant.invoices.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-receipt me-1"></i>{{ __('Tagihan') }}
                            </a>
                            <a href="{{ route('tenant.tickets.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-ticket-perforated me-1"></i>{{ __('Tiket') }}
                            </a>
                            <a href="{{ route('chat.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-chat-dots me-1"></i>{{ __('Chat') }}
                            </a>
                            <a href="{{ route('tenant.wishlist.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-heart me-1"></i>{{ __('Wishlist') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="text-uppercase text-muted mb-0">{{ __('Saran Peran') }}</h6>
                            <span class="badge bg-light text-dark">{{ __('Perbarui aktivitas harianmu') }}</span>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold">{{ __('Pembayaran & Tagihan') }}</span>
                                    <a href="{{ route('tenant.invoices.index') }}" class="small link-primary">{{ __('Buka') }}</a>
                                </div>
                                <small class="text-muted">{{ __('Cek status QRIS/manual, unggah bukti, dan unduh PDF.') }}</small>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold">{{ __('Kontrak & Properti') }}</span>
                                    <a href="{{ route('tenant.contracts.index') }}" class="small link-primary">{{ __('Buka') }}</a>
                                </div>
                                <small class="text-muted">{{ __('Lihat kontrak aktif, riwayat, dan rencana perpanjangan.') }}</small>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold">{{ __('Tiket & Chat') }}</span>
                                    <a href="{{ route('tenant.tickets.index') }}" class="small link-primary">{{ __('Buka') }}</a>
                                </div>
                                <small class="text-muted">{{ __('Buat tiket, ikuti progres SLA, dan jaga komunikasi dengan owner/admin.') }}</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
