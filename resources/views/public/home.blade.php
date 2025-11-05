@extends('layouts.public')

@section('content')
<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center text-center mb-4">
            <div class="col-lg-8">
                <span class="badge bg-primary-subtle text-primary text-uppercase fw-semibold mb-3">KostIn</span>
                <h1 class="display-5 fw-semibold">{{ __('Temukan kost impian Anda') }}</h1>
                <p class="lead text-muted">
                    {{ __('Jelajahi kost pilihan dengan fasilitas lengkap, pantau kontrak, dan selesaikan pembayaran via QRIS tanpa ribet.') }}
                </p>
            </div>
        </div>
        <form class="row g-3 justify-content-center">
            <div class="col-md-3">
                <label class="form-label">{{ __('Kata Kunci') }}</label>
                <input type="search" class="form-control" placeholder="{{ __('Nama kost, area, atau fasilitas') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('Harga Minimal') }}</label>
                <input type="number" class="form-control" placeholder="Rp0">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('Harga Maksimal') }}</label>
                <input type="number" class="form-control" placeholder="Rp3.000.000">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('Fasilitas') }}</label>
                <input type="text" class="form-control" placeholder="AC, Wi-Fi, Kamar mandi dalam">
            </div>
            <div class="col-12 col-md-10 col-lg-4 d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    {{ __('Cari Kost') }}
                </button>
            </div>
        </form>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary text-uppercase fw-semibold mb-3">{{ __('Populer') }}</span>
            <h2 class="fw-bold">{{ __('Rekomendasi kost dengan okupansi tinggi dan ulasan terbaik') }}</h2>
        </div>
        <div class="row g-4">
            @forelse ($properties as $property)
                @php
                    $photo = collect($property->photos)->first();
                    $startingPrice = collect($property->roomTypes)->flatMap->rooms->pluck('custom_price')->filter()->min()
                        ?? collect($property->roomTypes)->pluck('base_price')->filter()->min();
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        @if ($photo)
                            <img src="{{ $photo }}" class="card-img-top" alt="{{ $property->name }}">
                        @else
                            <div class="ratio ratio-4x3 bg-body-secondary d-flex align-items-center justify-content-center">
                                <span class="text-muted">{{ __('Belum ada foto') }}</span>
                            </div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-success-subtle text-success text-uppercase fw-semibold">{{ ucfirst($property->status) }}</span>
                                @if ($startingPrice)
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ __('Mulai Rp') }}{{ number_format($startingPrice, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                            <h5 class="card-title mb-2">
                                <a href="{{ route('property.show', $property) }}" class="text-decoration-none text-dark">
                                    {{ $property->name }}
                                </a>
                            </h5>
                            <p class="card-text text-muted flex-grow-1">{{ $property->address }}</p>
                            <div class="d-flex align-items-center mt-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($property->owner?->name) }}" class="rounded-circle me-3" width="40" height="40" alt="{{ $property->owner?->name }}">
                                <div>
                                    <p class="mb-0 fw-semibold">{{ $property->owner?->name }}</p>
                                    <small class="text-muted">{{ $property->owner?->email }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning mb-0">
                        {{ __('Belum ada properti aktif. Pemilik dapat mendaftarkan kost melalui portal owner.') }}
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-secondary-subtle text-secondary text-uppercase fw-semibold mb-3">{{ __('Kenapa KostIn?') }}</span>
            <h2 class="fw-bold">{{ __('Semua kebutuhan pengelolaan kost dalam satu platform') }}</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                    <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;">
                        <span class="fw-bold">1</span>
                    </div>
                        <h5 class="card-title">{{ __('Kontrak Digital') }}</h5>
                        <p class="card-text text-muted">
                            {{ __('Tandatangani dan arsipkan kontrak secara online lengkap dengan pengingat otomatis.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                    <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;">
                        <span class="fw-bold">2</span>
                    </div>
                        <h5 class="card-title">{{ __('Pembayaran QRIS') }}</h5>
                        <p class="card-text text-muted">
                            {{ __('Tagihan bulanan terintegrasi dengan Midtrans agar tenant dapat membayar dari aplikasi favorit.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                    <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;">
                        <span class="fw-bold">3</span>
                    </div>
                        <h5 class="card-title">{{ __('Dashboard Owner & Admin') }}</h5>
                        <p class="card-text text-muted">
                            {{ __('Pantau okupansi, tagihan tertunggak, dan tugas operasional dalam satu dasbor ringkas.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
