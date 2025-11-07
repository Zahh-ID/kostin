@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('owner.rooms.index', ['property_id' => optional($contextProperty)->id]) }}" class="text-decoration-none small text-muted">&larr; {{ __('Kembali') }}</a>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-2 mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Kamar') }} {{ $room->room_code }}</h1>
            <p class="text-muted mb-0">
                {{ $room->roomType?->property?->name }} &middot; {{ $room->roomType?->name }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('owner.rooms.edit', ['room' => $room, 'property_id' => optional($contextProperty)->id]) }}" class="btn btn-outline-primary btn-sm">
                {{ __('Ubah Detail') }}
            </a>
            <form method="POST" action="{{ route('owner.rooms.destroy', ['room' => $room, 'property_id' => optional($contextProperty)->id]) }}" onsubmit="return confirm('{{ __('Hapus kamar ini?') }}')">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger btn-sm" type="submit">{{ __('Hapus') }}</button>
            </form>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-7">
            <div class="card shadow-sm border-0 mb-4 mb-xl-0">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">{{ __('Informasi Kamar') }}</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">{{ __('Status') }}</div>
                            @php
                                $statusLabels = [
                                    'available' => __('Tersedia'),
                                    'occupied' => __('Terisi'),
                                    'maintenance' => __('Perbaikan'),
                                ];
                            @endphp
                            <span class="badge bg-primary-subtle text-primary-emphasis mt-1">
                                {{ $statusLabels[$room->status] ?? ucfirst($room->status) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">{{ __('Harga Khusus') }}</div>
                            <div class="fw-semibold">
                                {{ 'Rp'.number_format($room->custom_price ?? $room->roomType?->base_price ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">{{ __('Fasilitas Tambahan') }}</div>
                            @php
                                $facilities = collect($room->facilities_override_json);
                            @endphp
                            @if ($facilities->isEmpty())
                                <p class="text-muted mb-0">{{ __('Tidak ada fasilitas tambahan khusus kamar ini.') }}</p>
                            @else
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    @foreach ($facilities as $facility)
                                        <span class="badge bg-light text-dark">{{ $facility }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">{{ __('Harga Dasar Tipe') }}</div>
                            <div class="fw-semibold">Rp{{ number_format($room->roomType?->base_price ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">{{ __('Deposit') }}</div>
                            <div class="fw-semibold">Rp{{ number_format($room->roomType?->deposit ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="text-muted small">{{ __('Deskripsi') }}</div>
                        <p class="mb-0">{!! nl2br(e($room->description)) !!}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">{{ __('Foto Kamar') }}</h2>
                    <div class="row g-3 mb-4">
                        @forelse ($room->photos_json ?? [] as $photo)
                            @php
                                $photoUrl = \Illuminate\Support\Str::startsWith($photo, ['http://', 'https://'])
                                    ? $photo
                                    : asset('storage/'.$photo);
                            @endphp
                            <div class="col-6">
                                <div class="ratio ratio-4x3 rounded overflow-hidden">
                                    <img src="{{ $photoUrl }}" alt="Room photo" class="w-100 h-100 object-fit-cover">
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('Belum ada foto.') }}</p>
                        @endforelse
                    </div>

                    <h2 class="h6 text-uppercase text-muted mb-3">{{ __('Riwayat Kontrak Terkini') }}</h2>
                    @forelse ($room->contracts as $contract)
                        <div class="border rounded-3 p-3 mb-3">
                            <div class="fw-semibold">{{ $contract->tenant?->name ?? __('Tanpa Penyewa') }}</div>
                            <div class="text-muted small">
                                {{ optional($contract->start_date)->format('d M Y') }} -
                                {{ optional($contract->end_date)->format('d M Y') ?? __('Berjalan') }}
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis mt-2">
                                {{ ucfirst($contract->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('Belum ada kontrak yang tercatat untuk kamar ini.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
