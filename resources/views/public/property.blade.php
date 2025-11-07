@extends('layouts.public')

@section('content')
<section class="py-5 bg-white">
    <div class="container">
        @php
            $photos = collect($property->photos ?? []);
        @endphp

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                @if ($photos->isNotEmpty())
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="ratio ratio-16x9 rounded-3 overflow-hidden bg-body-secondary">
                                <img src="{{ $photos->first() }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="{{ $property->name }}">
                            </div>
                        </div>
                        @foreach ($photos->slice(1)->take(3) as $photo)
                            <div class="col-4">
                                <div class="ratio ratio-4x3 rounded-3 overflow-hidden bg-body-secondary">
                                    <img src="{{ $photo }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="{{ $property->name }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="ratio ratio-16x9 rounded-3 bg-body-secondary d-flex align-items-center justify-content-center">
                        <span class="text-muted">{{ __('Belum ada foto yang diunggah') }}</span>
                    </div>
                @endif
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h1 class="h4 fw-semibold mb-1">{{ $property->name }}</h1>
                        <p class="text-muted">{{ $property->address }}</p>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($property->owner?->name) }}" class="rounded-circle" width="48" height="48" alt="{{ $property->owner?->name }}">
                            <div>
                                <p class="mb-0 fw-semibold">{{ $property->owner?->name }}</p>
                                <small class="text-muted d-block">{{ $property->owner?->email }}</small>
                                <small class="text-muted">{{ $property->owner?->phone }}</small>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            @auth
                                @if (auth()->user()->role === \App\Models\User::ROLE_TENANT)
                                    <a href="{{ route('tenant.applications.create', ['property_id' => $property->id]) }}" class="btn btn-primary">
                                        {{ __('Ajukan Kontrak di Properti Ini') }}
                                    </a>
                                @else
                                    <a href="{{ route('tenant.dashboard') }}" class="btn btn-primary">{{ __('Buka Portal') }}</a>
                                @endif
                                <a href="{{ route('contact') }}" class="btn btn-outline-primary">{{ __('Hubungi Pemilik') }}</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">{{ __('Masuk untuk mengajukan sewa') }}</a>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary">{{ __('Daftar akun baru') }}</a>
                            @endauth
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-2">{{ __('Status Properti') }}</h6>
                        <span class="badge bg-success-subtle text-success text-uppercase fw-semibold">{{ ucfirst($property->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-semibold">{{ __('Peraturan & Deskripsi') }}</h5>
                        <p class="text-muted mb-0">
                            {!! $property->rules_text ? nl2br(e($property->rules_text)) : __('Peraturan kos belum diumumkan oleh pemilik.') !!}
                        </p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-semibold">{{ __('Tipe Kamar & Unit') }}</h5>
                        @forelse ($property->roomTypes as $roomType)
                            @php
                                $facilities = collect($roomType->facilities_json);
                            @endphp
                            <div class="border rounded-3 p-3 mb-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-2">
                                    <div>
                                        <h6 class="fw-semibold mb-1">{{ $roomType->name }}</h6>
                                        <small class="text-muted">
                                            {{ __('Luas :area m² · Kamar mandi :type', ['area' => $roomType->area_m2 ?? '–', 'type' => $roomType->bathroom_type ?? '–']) }}
                                        </small>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ __('Mulai Rp') }}{{ number_format($roomType->base_price ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                @if ($facilities->isNotEmpty())
                                    <p class="text-muted mb-3">
                                        {{ __('Fasilitas:') }} {{ $facilities->implode(', ') }}
                                    </p>
                                @endif
                                <div class="row g-3">
                                    @forelse ($roomType->rooms as $room)
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="rounded-3 border p-3 h-100">
                                                <p class="fw-semibold mb-1">{{ __('Kamar :code', ['code' => $room->room_code]) }}</p>
                                                <p class="text-muted small mb-1">{{ __('Status:') }} {{ ucfirst($room->status) }}</p>
                                                <p class="text-muted small mb-0">
                                                    {{ $room->custom_price ? __('Harga khusus: Rp').number_format($room->custom_price, 0, ',', '.') : __('Mengikuti harga dasar') }}
                                                </p>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="alert alert-light mb-0">{{ __('Belum ada kamar untuk tipe ini.') }}</div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-warning mb-0">{{ __('Belum ada tipe kamar yang terdaftar.') }}</div>
                        @endforelse
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-semibold">{{ __('Tugas Bersama') }}</h5>
                        <ul class="list-group list-group-flush">
                            @forelse ($property->sharedTasks as $task)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span>{{ $task->title }}</span>
                                    <small class="text-muted">
                                        {{ __('Jadwal berikutnya: :date', ['date' => optional($task->next_run_at)->format('d M Y') ?? __('Fleksibel')]) }}
                                    </small>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted">{{ __('Belum ada tugas terjadwal.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-semibold">{{ __('Lokasi') }}</h5>
                        <p class="text-muted mb-4">{{ $property->address }}</p>
                        <p class="small text-muted mb-0">
                            {{ __('Gunakan tombol kontak untuk berdiskusi langsung dengan pemilik mengenai ketersediaan kamar, jadwal survei, atau informasi tambahan lainnya.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
