<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Kontrak Saya') }}</h1>
            <small class="text-muted">{{ $withHistory ? __('Menampilkan seluruh riwayat kontrak Anda.') : __('Menampilkan kontrak aktif yang sedang berjalan.') }}</small>
        </div>
        <a href="{{ $withHistory ? route('tenant.contracts.index') : route('tenant.contracts.index', ['history' => 1]) }}" class="btn btn-outline-primary btn-sm">
            {{ $withHistory ? __('Tampilkan kontrak aktif saja') : __('Lihat riwayat kontrak') }}
        </a>
    </x-slot>

    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Properti & Kamar') }}</th>
                                <th>{{ __('Periode') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contracts as $contract)
                                @php
                                    $room = $contract->room;
                                    $roomType = $room?->roomType;
                                    $property = $roomType?->property;
                                    $statusMap = [
                                        'draft' => 'secondary',
                                        'submitted' => 'info',
                                        'active' => 'success',
                                        'pending_renewal' => 'warning',
                                        'terminated' => 'danger',
                                        'canceled' => 'dark',
                                        'expired' => 'secondary',
                                    ];
                                    $badge = $statusMap[$contract->status] ?? 'primary';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $property?->name ?? __('Properti') }}</div>
                                        <div class="text-muted small">
                                            {{ $property?->address ?? '-' }}<br>
                                            {{ __('Kamar') }} {{ $room?->room_code }} · {{ $roomType?->name }}
                                        </div>
                                    </td>
                                    <td class="text-muted small">
                                        {{ optional($contract->start_date)->format('d M Y') }} -
                                        {{ optional($contract->end_date)->format('d M Y') ?? __('Berjalan') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $badge }} text-uppercase">{{ $contract->status }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('tenant.contracts.show', $contract) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>{{ __('Detail') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        {{ $withHistory ? __('Belum ada riwayat kontrak.') : __('Belum ada kontrak aktif. Ajukan sewa melalui halaman properti.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($contracts instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="card-footer bg-white border-0">
                    {{ $contracts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
