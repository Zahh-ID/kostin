<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Dashboard') }}</h1>
            <small class="text-muted">{{ __('Navigasi utama KostIn sesuai peran Anda.') }}</small>
        </div>
    </x-slot>

    <div class="container-fluid">
        <div class="row g-3 mb-4">
            <div class="col-12 col-xl-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Selamat datang di KostIn!') }}</h5>
                        <p class="card-text text-muted">
                            {{ __('Gunakan ringkasan berikut untuk melompat langsung ke pekerjaan prioritas Anda.') }}
                        </p>
                        @if ($actions->isNotEmpty())
                            <div class="row g-2 mt-3">
                                @foreach ($actions as $action)
                                    <div class="col-12 col-md-6">
                                        <a href="{{ isset($action['route']) && \Illuminate\Support\Facades\Route::has($action['route']) ? route($action['route']) : '#' }}" class="btn btn-outline-primary w-100 text-start @if (! isset($action['route']) || ! \Illuminate\Support\Facades\Route::has($action['route'])) disabled @endif">
                                            <div class="fw-semibold">{{ $action['label'] }}</div>
                                            <div class="small text-muted">{{ $action['description'] ?? '' }}</div>
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
                                <strong>{{ __('Tenant') }}:</strong> {{ __('Cek tagihan di menu Tagihan dan lakukan pembayaran QRIS atau manual.') }}
                            </li>
                            <li class="mb-2">
                                <strong>{{ __('Owner') }}:</strong> {{ __('Tambahkan properti baru dan pantau okupansi kontrak aktif.') }}
                            </li>
                            <li>
                                <strong>{{ __('Admin') }}:</strong> {{ __('Moderasi properti yang menunggu approval serta pantau tiket support.') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if ($cards->isNotEmpty())
            <div class="row g-3">
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
            <div class="alert alert-info border-0 shadow-sm">
                {{ __('Tidak ada ringkasan tambahan untuk peran Anda. Gunakan menu navigasi untuk mulai bekerja.') }}
            </div>
        @endif
    </div>
</x-app-layout>
