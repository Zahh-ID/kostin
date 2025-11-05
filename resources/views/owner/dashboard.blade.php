<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Owner Dashboard') }}</h1>
            <small class="text-muted">{{ __('Ringkasan performa properti dan operasional kos Anda.') }}</small>
        </div>
        <a href="{{ route('owner.properties.create') }}" class="btn btn-primary btn-sm">
            {{ __('Tambah Properti') }}
        </a>
    </x-slot>

    <div class="container-fluid">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-uppercase text-muted small mb-1">{{ __('Total Properti') }}</p>
                        <h3 class="mb-0">{{ $properties->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-uppercase text-muted small mb-1">{{ __('Jumlah Kamar') }}</p>
                        <h3 class="mb-0">{{ $roomCount }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-uppercase text-muted small mb-1">{{ __('Kontrak Aktif') }}</p>
                        <h3 class="mb-0">{{ $activeContracts }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Tren Pendapatan 6 Bulan Terakhir') }}</h5>
                        <span class="badge bg-light text-dark">
                            {{ __('Total: Rp').number_format(collect($incomeTrend['data'])->sum(), 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="card-body">
                        <canvas id="owner-income-trend" height="180"></canvas>
                    </div>
                    <div class="card-footer bg-white text-muted small">
                        {{ __('Grafik menunjukkan total invoice lunas per bulan.') }}
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">{{ __('Distribusi Status Kamar') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($roomStatusBreakdown->isNotEmpty())
                            <canvas id="owner-room-status" height="200"></canvas>
                        @else
                            <p class="text-muted mb-0">{{ __('Belum ada data status kamar.') }}</p>
                        @endif
                    </div>
                    <div class="card-footer bg-white text-muted small">
                        {{ __('Ketahui sebaran kamar tersedia, terisi, dan perbaikan.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">{{ __('Daftar Properti') }}</h5>
                    </div>
                    <div class="card-body">
                        @forelse ($properties as $property)
                            <div class="border rounded-3 p-3 mb-3">
                                <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $property->name }}</h6>
                                        <small class="text-muted">{{ $property->address }}</small>
                                    </div>
                                    <span class="badge bg-info text-uppercase">{{ $property->status }}</span>
                                </div>
                                <div class="text-muted small mb-2">
                                    {{ $property->roomTypes->count() }} {{ __('tipe kamar') }} · {{ $property->roomTypes->flatMap->rooms->count() }} {{ __('unit') }}
                                </div>
                                <a href="{{ route('owner.properties.show', $property) }}" class="link-primary small">
                                    {{ __('Kelola Properti') }}
                                </a>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('Belum ada properti. Tambahkan properti pertama Anda.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">{{ __('Tagihan Tertunggak') }}</h5>
                    </div>
                    <div class="card-body">
                        @forelse ($overdueInvoices as $invoice)
                            <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-start">
                                <div>
                                    <strong class="d-block">{{ $invoice->contract?->room?->roomType?->property?->name ?? '-' }}</strong>
                                    <span class="text-muted small">
                                        {{ __('Jatuh tempo') }} {{ optional($invoice->due_date)->format('d M Y') }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-danger text-uppercase mb-1">{{ ucfirst($invoice->status) }}</span>
                                    <div class="fw-semibold">
                                        Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('Tidak ada tagihan tertunggak.') }}</p>
                        @endforelse
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">{{ __('Tugas Mendatang') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse ($upcomingTasks as $task)
                                <li class="list-group-item px-0">
                                    <div class="fw-semibold">{{ $task->title }}</div>
                                    <div class="text-muted small">
                                        {{ $task->property?->name }} · {{ optional($task->next_run_at)->format('d M Y') ?? __('Jadwal fleksibel') }}
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted small">
                                    {{ __('Tidak ada tugas terjadwal.') }}
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">{{ __('Kontrak Mendekati Jatuh Tempo') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @forelse ($nextExpiringContracts as $contract)
                                <div class="list-group-item px-0">
                                    <div class="fw-semibold">{{ $contract->tenant?->name ?? __('Tenant') }}</div>
                                    <div class="text-muted small">
                                        {{ $contract->room?->roomType?->property?->name }} · {{ optional($contract->end_date)->translatedFormat('d M Y') ?? __('Tanpa akhir') }}
                                    </div>
                                    <a href="{{ route('owner.contracts.show', $contract) }}" class="link-primary small">
                                        {{ __('Lihat Kontrak') }}
                                    </a>
                                </div>
                            @empty
                                <div class="text-muted small">{{ __('Tidak ada kontrak yang akan berakhir dalam 3 bulan ke depan.') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($contractExpirations->isNotEmpty())
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">{{ __('Aktivasi Kontrak per Bulan') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="owner-contract-activations" height="160"></canvas>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" integrity="sha384-lcVYgnlV8PXkqsGg4uWclqkv0I2mxZEs8NVzssnQy9X9iSvbOfMu8MLzumoyrm21" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const incomeCtx = document.getElementById('owner-income-trend');
            const incomeLabels = @json($incomeTrend['labels']);
            const incomeData = @json($incomeTrend['data']);

            if (incomeCtx && incomeLabels.length) {
                new Chart(incomeCtx, {
                    type: 'bar',
                    data: {
                        labels: incomeLabels,
                        datasets: [{
                            label: '{{ __('Pendapatan (Rp)') }}',
                            data: incomeData,
                            backgroundColor: 'rgba(25, 135, 84, 0.6)',
                            borderRadius: 4,
                        }],
                    },
                    options: {
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: context => new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        minimumFractionDigits: 0,
                                    }).format(context.parsed.y ?? 0),
                                },
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => new Intl.NumberFormat('id-ID').format(value),
                                },
                            },
                        },
                    },
                });
            }

            const roomStatusCtx = document.getElementById('owner-room-status');
            const roomStatusData = @json($roomStatusBreakdown);

            if (roomStatusCtx && Object.keys(roomStatusData).length) {
                const statusLabels = Object.keys(roomStatusData).map(status => status.replaceAll('_', ' ').toUpperCase());
                const statusValues = Object.values(roomStatusData);
                const statusColors = {
                    available: '#198754',
                    occupied: '#0d6efd',
                    maintenance: '#ffc107',
                };

                new Chart(roomStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusValues,
                            backgroundColor: Object.keys(roomStatusData).map(key => statusColors[key] ?? '#adb5bd'),
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                        },
                    },
                });
            }

            const contractCtx = document.getElementById('owner-contract-activations');
            const contractData = @json($contractExpirations);
            if (contractCtx && Object.keys(contractData).length) {
                new Chart(contractCtx, {
                    type: 'line',
                    data: {
                        labels: Object.keys(contractData),
                        datasets: [{
                            label: '{{ __('Kontrak aktif') }}',
                            data: Object.values(contractData),
                            tension: 0.3,
                            borderColor: 'rgba(255, 159, 64, 1)',
                            backgroundColor: 'rgba(255, 159, 64, 0.2)',
                            fill: true,
                            pointBackgroundColor: 'rgba(255, 159, 64, 1)',
                        }],
                    },
                    options: {
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } },
                    },
                });
            }
        });
    </script>
@endpush
