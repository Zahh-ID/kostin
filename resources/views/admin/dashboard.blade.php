<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Admin Dashboard') }}</h1>
            <small class="text-muted">{{ __('Statistik platform KostIn dan aktivitas terbaru.') }}</small>
        </div>
    </x-slot>

    <div class="container-fluid">
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-uppercase text-muted small mb-1">{{ __('Properti') }}</p>
                        <h4 class="mb-0">{{ $stats['properties_total'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-uppercase text-muted small mb-1">{{ __('Menunggu') }}</p>
                        <h4 class="mb-0">{{ $stats['properties_pending'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-uppercase text-muted small mb-1">{{ __('Tenant') }}</p>
                        <h4 class="mb-0">{{ $stats['tenants'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-uppercase text-muted small mb-1">{{ __('Owner') }}</p>
                        <h4 class="mb-0">{{ $stats['owners'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-uppercase text-muted small mb-1">{{ __('Kontrak Aktif') }}</p>
                        <h4 class="mb-0">{{ $stats['active_contracts'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-uppercase text-muted small mb-1">{{ __('Tagihan Tertunggak') }}</p>
                        <h4 class="mb-0">{{ $stats['overdue_invoices'] }}</h4>
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
                            {{ __('Total: Rp').number_format(collect($revenueTrend['data'])->sum(), 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="card-body">
                        <canvas id="admin-revenue-trend" height="180"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">{{ __('Registrasi Pengguna') }}</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="admin-registration-trend" height="180"></canvas>
                    </div>
                    <div class="card-footer bg-white text-muted small">
                        {{ __('Memperlihatkan jumlah akun baru yang dibuat per bulan.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">{{ __('Status Moderasi Properti') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($propertyStatusBreakdown->isNotEmpty())
                            <canvas id="admin-property-status" height="200"></canvas>
                        @else
                            <p class="text-muted mb-0">{{ __('Belum ada data moderasi properti.') }}</p>
                        @endif
                    </div>
                    <div class="card-footer bg-white text-muted small">
                        {{ __('Pantau distribusi status permintaan listing terbaru.') }}
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">{{ __('Status Tiket Support') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($ticketStatusBreakdown->isNotEmpty())
                            <canvas id="admin-ticket-status" height="200"></canvas>
                        @else
                            <p class="text-muted mb-0">{{ __('Belum ada tiket support tercatat.') }}</p>
                        @endif
                    </div>
                    <div class="card-footer bg-white text-muted small">
                        {{ __('Gunakan data ini untuk memprioritaskan tindak lanjut support.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Pengguna Baru') }}</h5>
                        @if (\Illuminate\Support\Facades\Route::has('admin.users.index'))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Kelola') }}</a>
                        @endif
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse ($recentUsers as $recentUser)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold">{{ $recentUser->name }}</div>
                                        <div class="text-muted small">{{ $recentUser->email }}</div>
                                    </div>
                                    <span class="badge bg-info text-uppercase">{{ $recentUser->role }}</span>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted small">
                                    {{ __('Belum ada pendaftaran baru.') }}
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Tiket Support Terbaru') }}</h5>
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Kelola Tiket') }}</a>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse ($recentTickets as $ticket)
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold">{{ $ticket->subject }}</div>
                                            <div class="text-muted small">
                                                {{ $ticket->reporter?->name ?? __('Pengguna') }} · {{ optional($ticket->created_at)->diffForHumans() }}
                                            </div>
                                        </div>
                                        <span class="badge bg-secondary text-uppercase">{{ $ticket->status }}</span>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        {{ __('Ditugaskan ke :name', ['name' => $ticket->assignee?->name ?? __('Belum ditetapkan')]) }}
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted small">
                                    {{ __('Belum ada tiket masuk.') }}
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Tagihan Terbaru') }}</h5>
                        @if (\Illuminate\Support\Facades\Route::has('admin.moderations.index'))
                            <a href="{{ route('admin.moderations.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Moderasi Properti') }}</a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th scope="col">{{ __('Properti') }}</th>
                                    <th scope="col">{{ __('Tenant') }}</th>
                                    <th scope="col">{{ __('Status') }}</th>
                                    <th scope="col" class="text-end">{{ __('Jumlah') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($recentInvoices as $invoice)
                                    <tr>
                                        <td class="fw-semibold">{{ $invoice->contract?->room?->roomType?->property?->name }}</td>
                                        <td class="text-muted small">{{ $invoice->contract?->tenant?->name }}</td>
                                        <td><span class="badge bg-secondary text-uppercase">{{ $invoice->status }}</span></td>
                                        <td class="text-end fw-semibold">Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            {{ __('Belum ada data tagihan.') }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Properti Menunggu Moderasi') }}</h5>
                        <a href="{{ route('admin.moderations.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Buka Moderasi') }}</a>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse ($pendingModerations as $property)
                                <li class="list-group-item px-0">
                                    <div class="fw-semibold">{{ $property->name }}</div>
                                    <div class="text-muted small">
                                        {{ $property->owner?->name ?? __('Pemilik tidak tersedia') }} · {{ optional($property->updated_at)->diffForHumans() }}
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted small">
                                    {{ __('Tidak ada properti menunggu moderasi.') }}
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script>
        const initAdminDashboardCharts = () => {
            if (typeof window.Chart === 'undefined') {
                console.warn('Chart.js tidak tersedia. Pastikan aset Vite telah dikompilasi.');
                return;
            }

            const revenueCtx = document.getElementById('admin-revenue-trend');
            const revenueLabels = @json($revenueTrend['labels']);
            const revenueData = @json($revenueTrend['data']);

            if (revenueCtx && revenueLabels.length) {
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: revenueLabels,
                        datasets: [{
                            label: '{{ __('Pendapatan (Rp)') }}',
                            data: revenueData,
                            tension: 0.35,
                            fill: true,
                            borderColor: 'rgba(13, 110, 253, 1)',
                            backgroundColor: 'rgba(13, 110, 253, 0.15)',
                            pointRadius: 4,
                            pointBackgroundColor: 'rgba(13, 110, 253, 1)',
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

            const registrationCtx = document.getElementById('admin-registration-trend');
            const registrationLabels = @json($registrationTrend['labels']);
            const registrationData = @json($registrationTrend['data']);

            if (registrationCtx && registrationLabels.length) {
                new Chart(registrationCtx, {
                    type: 'bar',
                    data: {
                        labels: registrationLabels,
                        datasets: [{
                            data: registrationData,
                            backgroundColor: 'rgba(25, 135, 84, 0.7)',
                            borderRadius: 4,
                        }],
                    },
                    options: {
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } },
                    },
                });
            }

            const propertyStatusCtx = document.getElementById('admin-property-status');
            const propertyStatusData = @json($propertyStatusBreakdown);

            if (propertyStatusCtx && Object.keys(propertyStatusData).length) {
                const labels = Object.keys(propertyStatusData).map(status => status.replaceAll('_', ' ').toUpperCase());
                const values = Object.values(propertyStatusData);
                const colors = {
                    draft: '#6c757d',
                    pending: '#ffc107',
                    approved: '#198754',
                    rejected: '#dc3545',
                };

                new Chart(propertyStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: Object.keys(propertyStatusData).map(status => colors[status] ?? '#adb5bd'),
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        plugins: {
                            legend: { position: 'bottom' },
                        },
                    },
                });
            }

            const ticketStatusCtx = document.getElementById('admin-ticket-status');
            const ticketStatusData = @json($ticketStatusBreakdown);

            if (ticketStatusCtx && Object.keys(ticketStatusData).length) {
                const labels = Object.keys(ticketStatusData).map(status => status.replaceAll('_', ' ').toUpperCase());
                const values = Object.values(ticketStatusData);
                const colors = {
                    open: '#0d6efd',
                    in_review: '#ffc107',
                    escalated: '#dc3545',
                    resolved: '#198754',
                    rejected: '#6c757d',
                };

                new Chart(ticketStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: Object.keys(ticketStatusData).map(status => colors[status] ?? '#adb5bd'),
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        plugins: { legend: { position: 'bottom' } },
                    },
                });
            }
        };
        const bootCharts = () => {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAdminDashboardCharts, { once: true });
            } else {
                initAdminDashboardCharts();
            }
        };

        if (typeof window.Chart === 'undefined') {
            window.addEventListener('chart:ready', bootCharts, { once: true });
        } else {
            bootCharts();
        }
    </script>
@endpush
