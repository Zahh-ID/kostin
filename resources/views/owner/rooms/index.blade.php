@extends('layouts.app')

@section('content')
<div class="container py-4">
    @php
        $propertyId = $contextProperty->id ?? null;
    @endphp

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <p class="text-muted small mb-1">{{ __('Mengelola Kamar') }}</p>
            <h1 class="h4 fw-semibold mb-1">{{ $contextProperty->name }}</h1>
            <p class="text-muted mb-0">{{ $contextProperty->address }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('owner.properties.show', $contextProperty) }}" class="btn btn-outline-secondary btn-sm">
                {{ __('Lihat Properti') }}
            </a>
            <a href="{{ route('owner.rooms.create', ['property_id' => $propertyId]) }}" class="btn btn-primary btn-sm">
                {{ __('Tambah Kamar') }}
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between gap-3">
            <div>
                <p class="text-muted small mb-1">{{ __('Ringkasan Kamar') }}</p>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="display-6 fw-semibold">{{ $contextProperty->available_rooms ?? 0 }}</span>
                    <span class="text-muted">{{ __('tersedia dari :total kamar', ['total' => $contextProperty->total_rooms ?? 0]) }}</span>
                </div>
            </div>
            <div class="text-muted small">
                {{ __('Gunakan tombol di atas untuk menambah kamar baru atau memperbarui detail kamar yang sudah ada.') }}
            </div>
        </div>
    </div>

    @if ($filteredRoomType)
        <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
            <div>
                {{ __('Menampilkan kamar untuk tipe: :type', ['type' => $filteredRoomType->name]) }}
            </div>
            <a href="{{ route('owner.rooms.index', ['property_id' => $propertyId]) }}" class="btn btn-sm btn-outline-secondary">
                {{ __('Tampilkan Semua Kamar') }}
            </a>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Kode') }}</th>
                            <th>{{ __('Deskripsi Singkat') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Harga') }}</th>
                            <th>{{ __('Foto') }}</th>
                            <th class="text-end">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rooms as $room)
                            <tr>
                                <td class="fw-semibold">{{ $room->room_code }}</td>
                                <td>
                                    <div class="text-muted small mb-1">{{ __('Tipe:') }} {{ $room->roomType?->name ?? __('Default') }}</div>
                                    <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($room->description, 80) }}</div>
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'available' => 'bg-success-subtle text-success-emphasis',
                                            'occupied' => 'bg-primary-subtle text-primary-emphasis',
                                            'maintenance' => 'bg-warning-subtle text-warning-emphasis',
                                        ];
                                    @endphp
                                    <span class="badge rounded-pill {{ $statusClasses[$room->status] ?? 'bg-secondary-subtle text-secondary-emphasis' }}">
                                        {{ __($room->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{ 'Rp'.number_format($room->custom_price ?? $room->roomType?->base_price ?? 0, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ count($room->photos_json ?? []) }} {{ __('foto') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('owner.rooms.show', ['room' => $room, 'property_id' => $propertyId]) }}" class="btn btn-outline-primary">
                                            {{ __('Detail') }}
                                        </a>
                                        <a href="{{ route('owner.rooms.edit', ['room' => $room, 'property_id' => $propertyId]) }}" class="btn btn-outline-secondary">
                                            {{ __('Ubah') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    {{ __('Belum ada kamar di properti ini.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($rooms instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="card-footer bg-white">
                {{ $rooms->appends(['property_id' => $propertyId])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
