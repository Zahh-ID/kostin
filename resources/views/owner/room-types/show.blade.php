@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('owner.room-types.index') }}" class="text-decoration-none small text-muted">&larr; Kembali</a>
    <div class="d-flex justify-content-between align-items-start mt-2 mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ $roomType->name }}</h1>
            <p class="text-muted mb-0">{{ $roomType->property?->name }}</p>
        </div>
        <a href="{{ route('owner.room-types.edit', $roomType) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h2 class="h6 fw-semibold mb-0">Spesifikasi</h2>
                </div>
                <div class="card-body">
                    <dl class="row small mb-0">
                        <dt class="col-sm-4 text-muted">Luas</dt>
                        <dd class="col-sm-8">{{ $roomType->area_m2 ?? '-' }} m²</dd>
                        <dt class="col-sm-4 text-muted">Tipe Kamar Mandi</dt>
                        <dd class="col-sm-8">{{ ucfirst($roomType->bathroom_type ?? '-') }}</dd>
                        <dt class="col-sm-4 text-muted">Harga Dasar</dt>
                        <dd class="col-sm-8">Rp{{ number_format($roomType->base_price ?? 0, 0, ',', '.') }}</dd>
                        <dt class="col-sm-4 text-muted">Deposit</dt>
                        <dd class="col-sm-8">Rp{{ number_format($roomType->deposit ?? 0, 0, ',', '.') }}</dd>
                    </dl>
                    <div class="mt-3">
                        <h3 class="small text-uppercase text-muted mb-2">Fasilitas</h3>
                        <p class="text-muted mb-0">{{ collect($roomType->facilities_json)->implode(', ') ?: 'Belum diatur' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-semibold mb-0">Unit Kamar</h2>
                    <a href="{{ route('owner.rooms.create') }}" class="btn btn-outline-primary btn-sm">Tambah Kamar</a>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @forelse ($roomType->rooms as $room)
                            <div class="col-md-6">
                                <div class="border rounded p-2 h-100">
                                    <p class="fw-semibold mb-1">Kamar {{ $room->room_code }}</p>
                                    <p class="small text-muted mb-1">Status: {{ ucfirst($room->status) }}</p>
                                    <p class="small text-muted mb-0">Harga Custom: {{ $room->custom_price ? 'Rp'.number_format($room->custom_price, 0, ',', '.') : '-' }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted mb-0">Belum ada kamar untuk tipe ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
