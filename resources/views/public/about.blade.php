@extends('layouts.public')

@section('content')
<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6">
                <h1 class="display-6 fw-semibold">{{ __('Tentang') }} {{ config('app.name') }}</h1>
                <p class="text-muted mb-4">
                    {{ __('KostIn membantu pemilik dan penyewa kos berkolaborasi di satu platform. Kami menyediakan backend siap produksi untuk pengelolaan properti, kontrak digital, penagihan terintegrasi Midtrans, dan dokumentasi API lengkap.') }}
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('contact') }}" class="btn btn-primary">{{ __('Hubungi Kami') }}</a>
                    <a href="{{ route('home') }}" class="btn btn-outline-primary">{{ __('Jelajahi Kost') }}</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    @foreach ([
                        'https://tailwindui.com/img/logos/transistor-logo-gray-400.svg',
                        'https://tailwindui.com/img/logos/mirage-logo-gray-400.svg',
                        'https://tailwindui.com/img/logos/tuple-logo-gray-400.svg',
                        'https://tailwindui.com/img/logos/laravel-logo-gray-400.svg',
                        'https://tailwindui.com/img/logos/statickit-logo-gray-400.svg',
                        'https://tailwindui.com/img/logos/statamic-logo-gray-400.svg',
                    ] as $logo)
                        <div class="col-6 col-md-4">
                            <div class="border rounded-3 bg-light p-3 h-100 d-flex align-items-center justify-content-center">
                                <img src="{{ $logo }}" alt="Partner logo" class="img-fluid" style="max-height:40px;">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-secondary-subtle text-secondary text-uppercase fw-semibold mb-3">{{ __('Visi & Misi') }}</span>
            <h2 class="fw-bold">{{ __('Menghadirkan pengalaman digital terbaik untuk bisnis kos') }}</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Visi') }}</h5>
                        <p class="card-text text-muted">
                            {{ __('Menjadi sistem backend andal bagi usaha kos di Indonesia sehingga operasional harian lebih efisien dan penyewa memperoleh pengalaman digital yang nyaman.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Misi') }}</h5>
                        <ul class="text-muted mb-0">
                            <li>{{ __('Menyediakan alat digital untuk mengelola properti dan kontrak secara transparan.') }}</li>
                            <li>{{ __('Mengotomatiskan penagihan dan pembayaran dengan QRIS untuk mempercepat arus kas.') }}</li>
                            <li>{{ __('Mendukung developer melalui dokumentasi API dan arsitektur modular.') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
