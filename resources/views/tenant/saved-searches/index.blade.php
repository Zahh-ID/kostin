<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Pencarian Tersimpan') }}</h1>
            <small class="text-muted">{{ __('Simpan dan kelola filter pencarian favorit Anda.') }}</small>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm">
            {{ __('Mulai Cari Kos') }}
        </a>
    </x-slot>

    <div class="container-fluid">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
            </div>
        @endif

        @if (! $savedSearches->count())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="display-4 mb-3" aria-hidden="true">💾</div>
                    <h2 class="h5">{{ __('Belum ada pencarian tersimpan') }}</h2>
                    <p class="text-muted mb-4">{{ __('Simpan filter pencarian Anda untuk mendapatkan notifikasi kos terbaru.') }}</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        {{ __('Mulai Cari Kos') }}
                    </a>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="list-group list-group-flush">
                    @foreach ($savedSearches as $savedSearch)
                        @php
                            $filters = collect($savedSearch->filters ?? []);
                            $summary = [];

                            if ($filters->get('search')) {
                                $summary[] = __('Kata kunci: :value', ['value' => $filters->get('search')]);
                            }

                            if ($filters->get('city')) {
                                $summary[] = __('Kota: :city', ['city' => $filters->get('city')]);
                            }

                            if ($filters->get('type')) {
                                $summary[] = __('Tipe: :type', ['type' => ucfirst($filters->get('type'))]);
                            }

                            $hasPrice = $filters->has('minPrice') || $filters->has('maxPrice');
                            if ($hasPrice) {
                                $min = $filters->get('minPrice');
                                $max = $filters->get('maxPrice');
                                $priceLabel = match (true) {
                                    $min && $max => __('Harga: Rp:min - Rp:max', ['min' => number_format($min, 0, ',', '.'), 'max' => number_format($max, 0, ',', '.')]),
                                    $min => __('Harga: ≥ Rp:min', ['min' => number_format($min, 0, ',', '.')]),
                                    $max => __('Harga: ≤ Rp:max', ['max' => number_format($max, 0, ',', '.')]),
                                    default => null,
                                };
                                if ($priceLabel) {
                                    $summary[] = $priceLabel;
                                }
                            }

                            if (is_array($filters->get('facilities')) && count($filters->get('facilities')) > 0) {
                                $summary[] = __('Fasilitas: :count dipilih', ['count' => count($filters->get('facilities'))]);
                            }
                        @endphp
                        <div class="list-group-item py-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h2 class="h5 mb-0">{{ $savedSearch->name }}</h2>
                                        <span class="badge {{ $savedSearch->notification_enabled ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $savedSearch->notification_enabled ? __('Notifikasi Aktif') : __('Notifikasi Nonaktif') }}
                                        </span>
                                    </div>
                                    @if (! empty($summary))
                                        <div class="text-muted small mb-2">
                                            {{ implode(' • ', $summary) }}
                                        </div>
                                    @endif
                                    <div class="text-muted small">
                                        {{ __('Dibuat pada :date', ['date' => optional($savedSearch->created_at)->translatedFormat('d M Y') ?? '—']) }}
                                        @if ($savedSearch->last_notified_at)
                                            · {{ __('Notifikasi terakhir: :date', ['date' => $savedSearch->last_notified_at->diffForHumans()]) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('tenant.saved-searches.apply', $savedSearch) }}" class="btn btn-outline-primary btn-sm">
                                        {{ __('Terapkan Filter') }}
                                    </a>
                                    <form action="{{ route('tenant.saved-searches.destroy', $savedSearch) }}" method="post" onsubmit="return confirm('{{ __('Hapus pencarian tersimpan ini?') }}');">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            {{ __('Hapus') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($savedSearches instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="mt-4">
                    {{ $savedSearches->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
