<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Wishlist Saya') }}</h1>
            <small class="text-muted">{{ __('Properti kos yang Anda simpan untuk dipantau.') }}</small>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm">
            {{ __('Jelajahi Kos') }}
        </a>
    </x-slot>

    <div class="container-fluid">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
            </div>
        @endif

        @if (! $wishlistItems->count())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="display-4 mb-3" aria-hidden="true">❤</div>
                    <h2 class="h5">{{ __('Wishlist Anda kosong') }}</h2>
                    <p class="text-muted mb-4">{{ __('Mulai simpan properti favorit agar mudah ditemukan kembali.') }}</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        {{ __('Mulai Cari Kos') }}
                    </a>
                </div>
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @foreach ($wishlistItems as $item)
                    @php
                        $property = $item->property;
                        $photo = $property?->photos[0] ?? 'https://via.placeholder.com/640x400.png?text=KostIn';
                        $roomTypes = collect($property?->roomTypes ?? []);
                        $basePrice = $roomTypes->pluck('base_price')->filter()->min();
                        $rooms = $roomTypes->flatMap(fn ($type) => collect($type->rooms ?? []));
                        $availableRooms = $rooms->where('status', 'available')->count();
                        $totalRooms = $rooms->count();
                    @endphp
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="ratio ratio-16x9 rounded-top overflow-hidden">
                                <img src="{{ $photo }}" alt="{{ $property?->name ?? __('Properti tidak tersedia') }}" class="w-100 h-100" style="object-fit: cover;">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h2 class="h5 mb-1">{{ $property?->name ?? __('Properti tidak tersedia') }}</h2>
                                        <p class="text-muted small mb-0">
                                            {{ \Illuminate\Support\Str::limit($property?->address ?? __('Alamat tidak tersedia'), 70) }}
                                        </p>
                                    </div>
                                    <span class="fs-5 text-danger" aria-hidden="true">❤</span>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex flex-wrap gap-2 small text-muted">
                                        <span>{{ __('Tipe kamar: :count', ['count' => $roomTypes->count()]) }}</span>
                                        <span class="text-muted">•</span>
                                        <span>
                                            {{ __('Kamar tersedia: :available/:total', ['available' => $availableRooms, 'total' => $totalRooms]) }}
                                        </span>
                                    </div>
                                    @if ($basePrice)
                                        <div class="fw-semibold mt-2">
                                            {{ __('Mulai dari') }} Rp{{ number_format($basePrice, 0, ',', '.') }} / {{ __('bulan') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-auto d-flex flex-wrap gap-2">
                                    @if ($property)
                                        <a href="{{ route('property.show', $property) }}" class="btn btn-outline-primary btn-sm">
                                            {{ __('Lihat Detail') }}
                                        </a>
                                    @endif
                                    <form action="{{ route('tenant.wishlist.destroy', $item) }}" method="post" onsubmit="return confirm('{{ __('Hapus properti ini dari wishlist?') }}');">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            {{ __('Hapus') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($wishlistItems instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="mt-4">
                    {{ $wishlistItems->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
