<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column gap-1">
            <h1 class="h4 mb-0 text-dark">{{ __('Cari Properti & Ajukan Kontrak') }}</h1>
            <small class="text-muted">{{ __('Filter properti yang tersedia dan ajukan kontrak langsung.') }}</small>
        </div>
    </x-slot>

    @php
    $hasOverdue = auth()->user()?->invoices()->whereIn('invoices.status', ['overdue', 'unpaid'])->exists();
@endphp

    <div class="container-fluid">
        @if ($hasOverdue)
            <div class="alert alert-warning d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ __('Tagihan tertunggak terdeteksi.') }}</strong>
                    <div class="text-muted small">{{ __('Selesaikan pembayaran sebelum mengajukan kontrak baru.') }}</div>
                </div>
                <a href="{{ route('tenant.invoices.index') }}" class="btn btn-sm btn-primary">{{ __('Bayar Tagihan') }}</a>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form method="get" class="row g-3" action="{{ route('tenant.search') }}">
                    <div class="col-lg-4">
                        <label class="form-label small text-muted">{{ __('Lokasi / Nama') }}</label>
                        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Cari nama properti atau lokasi') }}">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label small text-muted">{{ __('Harga Min (Rp)') }}</label>
                        <input type="number" name="price_min" class="form-control" value="{{ request('price_min') }}" min="0">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label small text-muted">{{ __('Harga Max (Rp)') }}</label>
                        <input type="number" name="price_max" class="form-control" value="{{ request('price_max') }}" min="0">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label small text-muted">{{ __('Tipe Kamar') }}</label>
                        <input type="text" name="room_type" class="form-control" value="{{ request('room_type') }}" placeholder="{{ __('Studio / Deluxe') }}">
                    </div>
                    <div class="col-lg-2 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="available_only" value="1" id="available_only" @checked(request('available_only'))>
                            <label class="form-check-label" for="available_only">
                                {{ __('Hanya yang tersedia') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">{{ __('Cari') }}</button>
                        <a href="{{ route('tenant.search') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
                    </div>
                </form>
            </div>
        </div>

        @if ($properties->isEmpty())
            <div class="alert alert-info border-0 shadow-sm">{{ __('Tidak ada hasil. Sesuaikan filter pencarian Anda.') }}</div>
        @else
            <div class="row g-3">
                @foreach ($properties as $property)
                    <div class="col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            @if ($property->cover_url)
                                <div class="ratio ratio-4x3 rounded-top overflow-hidden">
                                    <img src="{{ $property->cover_url }}" alt="{{ $property->name }}" class="w-100 h-100" style="object-fit: cover;">
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="mb-1">{{ $property->name }}</h5>
                                        <p class="text-muted small mb-0">{{ $property->address }}</p>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary">{{ __('Approved') }}</span>
                                </div>
                                <div class="text-muted small mb-2">{{ __('Harga mulai') }} Rp{{ number_format($property->min_price ?? 0, 0, ',', '.') }}</div>
                                @if ($property->available_rooms_count)
                                    <div class="mb-2">
                                        <span class="badge bg-success-subtle text-success">{{ $property->available_rooms_count }} {{ __('kamar tersedia') }}</span>
                                    </div>
                                @else
                                    <div class="mb-2">
                                        <span class="badge bg-secondary">{{ __('Tidak ada kamar aktif') }}</span>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <p class="text-muted small mb-1">{{ $property->preview_rules ?? __('Belum ada peraturan tertulis.') }}</p>
                                    @if ($property->roomTypes->isNotEmpty())
                                        <div class="small text-muted">{{ __('Tipe:') }} {{ $property->roomTypes->take(2)->pluck('name')->implode(', ') }}@if($property->roomTypes->count() > 2){{ ' +' . ($property->roomTypes->count() - 2) }}@endif</div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">
                                <a href="{{ route('tenant.properties.apply', $property) }}" class="btn btn-sm btn-outline-primary @if($hasOverdue) disabled @endif">
                                    {{ __('Ajukan Kontrak') }}
                                </a>
                                <a href="{{ route('tenant.properties.apply', $property) }}" class="small link-primary">{{ __('Detail & pilih kamar') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($properties instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="d-flex justify-content-end mt-3">
                    {{ $properties->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
