<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column gap-1">
            <h1 class="h4 mb-0 text-dark">{{ __('Ajukan Kontrak') }}</h1>
            <small class="text-muted">{{ __('Tinjau detail properti dan pilih kamar sebelum lanjut ke formulir.') }}</small>
        </div>
    </x-slot>

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
            @if ($coverUrl)
                <div class="ratio ratio-16x9 rounded-top overflow-hidden">
                    <img src="{{ $coverUrl }}" alt="{{ $property->name }}" class="w-100 h-100" style="object-fit: cover;">
                </div>
            @endif
            <div class="card-body">
                <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                    <div>
                        <h5 class="mb-1">{{ $property->name }}</h5>
                        <p class="text-muted small mb-0">{{ $property->address }}</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">{{ __('Approved') }}</span>
                </div>
                <p class="text-muted small mb-2">{!! nl2br(e($property->rules_text ?: __('Belum ada peraturan tertulis.'))) !!}</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('tenant.applications.create', ['property_id' => $property->id]) }}" class="btn btn-primary @if($hasOverdue) disabled @endif">
                        {{ __('Lanjut ke Formulir') }}
                    </a>
                    <a href="{{ route('tenant.search') }}" class="btn btn-light">{{ __('Kembali ke Pencarian') }}</a>
                </div>
            </div>
        </div>

        <div class="row g-3">
            @forelse ($property->roomTypes as $roomType)
                <div class="col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">{{ $roomType->name }}</h6>
                                    <p class="text-muted small mb-0">{{ __('Harga dasar') }} Rp{{ number_format($roomType->base_price ?? 0, 0, ',', '.') }}</p>
                                </div>
                                <span class="badge bg-light text-dark">{{ $roomType->rooms->count() }} {{ __('kamar') }}</span>
                            </div>
                            @if ($roomType->rooms->isNotEmpty())
                                <div class="small text-muted mb-2">{{ __('Kamar tersedia:') }} {{ $roomType->rooms->pluck('room_code')->implode(', ') }}</div>
                            @else
                                <div class="small text-muted mb-2">{{ __('Belum ada kamar aktif.') }}</div>
                            @endif
                            @if ($roomType->facilities_json)
                                <div class="small text-muted mb-2">{{ __('Fasilitas:') }} {{ collect($roomType->facilities_json)->implode(', ') }}</div>
                            @endif
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <a href="{{ route('tenant.applications.create', ['property_id' => $property->id, 'room_type_id' => $roomType->id]) }}" class="btn btn-sm btn-outline-primary @if($hasOverdue) disabled @endif">
                                    {{ __('Pilih & Ajukan') }}
                                </a>
                                <span class="text-muted small">{{ __('Mulai') }} Rp{{ number_format($roomType->base_price ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info border-0 shadow-sm">{{ __('Belum ada tipe kamar aktif.') }}</div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
