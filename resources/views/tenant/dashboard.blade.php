<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Halo, :name', ['name' => auth()->user()->name]) }}</h1>
            <small class="text-muted">{{ __('Pantau kontrak, tagihan, dan aktivitas pembayaran kamu.') }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tenant.invoices.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-receipt me-1"></i>{{ __('Lihat Tagihan') }}
            </a>
            <a href="{{ route('tenant.tickets.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-chat-dots me-1"></i>{{ __('Tiket & Chat') }}
            </a>
        </div>
    </x-slot>

    <div class="container-fluid">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase text-muted small mb-1">{{ __('Kontrak Aktif') }}</p>
                            <h3 class="mb-0">{{ $activeContracts->count() }}</h3>
                            <small class="text-muted">{{ __('Kontrak berjalan & dapat diunduh PDF') }}</small>
                        </div>
                        <span class="badge bg-primary-subtle text-primary-emphasis p-2"><i class="bi bi-file-earmark-text"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase text-muted small mb-1">{{ __('Tagihan Jatuh Tempo') }}</p>
                            <h3 class="mb-0">{{ $dueInvoices->count() }}</h3>
                            <small class="text-muted">{{ __('Segera selesaikan sebelum denda berjalan') }}</small>
                        </div>
                        <span class="badge bg-warning-subtle text-warning-emphasis p-2"><i class="bi bi-exclamation-triangle"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase text-muted small mb-1">{{ __('Total Tertunggak') }}</p>
                            <h3 class="mb-0">Rp{{ number_format($totalOutstanding, 0, ',', '.') }}</h3>
                            <small class="text-muted">{{ __('Termasuk late fee jika ada keterlambatan') }}</small>
                        </div>
                        <span class="badge bg-danger-subtle text-danger-emphasis p-2"><i class="bi bi-graph-down"></i></span>
                    </div>
                </div>
            </div>
        </div>

        @if ($nextDueInvoice)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="badge bg-warning-subtle text-warning-emphasis mb-2"><i class="bi bi-alarm me-1"></i>{{ __('Tagihan Terdekat') }}</div>
                        <h5 class="mb-1">{{ $nextDueInvoice->contract?->room?->roomType?->property?->name ?? __('Tagihan') }}</h5>
                        <p class="mb-0 text-muted small">
                            {{ __('Jatuh tempo :date · Total :amount', [
                                'date' => optional($nextDueInvoice->due_date)->translatedFormat('d M Y') ?? '—',
                                'amount' => 'Rp'.number_format($nextDueInvoice->total ?? 0, 0, ',', '.'),
                            ]) }}
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('tenant.invoices.show', $nextDueInvoice) }}" class="btn btn-primary">
                            <i class="bi bi-credit-card me-1"></i>{{ __('Bayar Sekarang') }}
                        </a>
                        <a href="{{ route('tenant.invoices.pdf', $nextDueInvoice) }}" class="btn btn-outline-secondary" target="_blank">
                            <i class="bi bi-file-earmark-arrow-down me-1"></i>{{ __('Unduh PDF') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Kontrak Aktif') }}</h5>
                        <a href="{{ route('tenant.contracts.index') }}" class="link-primary small">{{ __('Lihat semua') }}</a>
                    </div>
                    <div class="card-body">
                        @forelse ($activeContracts as $contract)
                            @php
                                $room = $contract->room;
                                $roomType = $room?->roomType;
                                $property = $roomType?->property;
                            @endphp
                            <div class="d-flex justify-content-between align-items-start border rounded-3 p-3 mb-3">
                                <div>
                                    <h6 class="mb-1">{{ $property?->name ?? __('Unnamed Property') }}</h6>
                                    <div class="text-muted small mb-1">
                                        {{ __('Kamar') }} {{ $room?->room_code }} · {{ $roomType?->name }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ __('Periode') }} {{ optional($contract->start_date)->format('d M Y') }} – {{ optional($contract->end_date)->format('d M Y') ?? __('Berjalan') }}
                                    </div>
                                </div>
                                <a href="{{ route('tenant.contracts.show', $contract) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('Belum ada kontrak aktif. Ajukan sewa melalui halaman properti.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Tagihan Mendatang') }}</h5>
                        <a href="{{ route('tenant.invoices.index') }}" class="link-primary small">{{ __('Semua tagihan') }}</a>
                    </div>
                    <div class="card-body">
                        @forelse ($dueInvoices as $invoice)
                            <div class="d-flex justify-content-between align-items-start border rounded-3 p-3 mb-3">
                                <div>
                                    <strong class="d-block mb-1">{{ $invoice->contract?->room?->roomType?->property?->name }}</strong>
                                    <span class="text-muted small d-block">
                                        {{ __('Jatuh tempo') }} {{ optional($invoice->due_date)->translatedFormat('d M Y') ?? '–' }}
                                    </span>
                                    <span class="text-muted small">{{ __('Periode') }} {{ $invoice->period_month }}/{{ $invoice->period_year }}</span>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $invoice->status === 'overdue' ? 'danger' : 'warning' }} text-uppercase">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                    <div class="fw-semibold mt-1">
                                        Rp{{ number_format($invoice->total, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('Tidak ada tagihan yang perlu dibayar.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Tren Pembayaran 6 Bulan Terakhir') }}</h5>
                        <span class="badge bg-light text-dark">{{ __('Total: Rp').number_format(collect($paymentTrend['data'])->sum(), 0, ',', '.') }}</span>
                    </div>
                    <div class="card-body">
                        <canvas id="tenant-payment-trend" height="180"></canvas>
                    </div>
                    <div class="card-footer bg-white text-muted small">
                        {{ __('Grafik menunjukkan total pembayaran yang berhasil setiap bulan.') }}
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">{{ __('Status Tagihan') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($invoiceStatusBreakdown->isNotEmpty())
                            <canvas id="tenant-invoice-status" height="200"></canvas>
                        @else
                            <p class="text-muted mb-0">{{ __('Belum ada data status tagihan.') }}</p>
                        @endif
                    </div>
                    <div class="card-footer bg-white text-muted small">
                        {{ __('Pantau distribusi status tagihan Anda saat ini.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Pembayaran Terbaru') }}</h5>
                        <a href="{{ route('tenant.invoices.index') }}" class="link-primary small">{{ __('Lihat riwayat') }}</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th scope="col">{{ __('Tagihan') }}</th>
                                    <th scope="col">{{ __('Properti') }}</th>
                                    <th scope="col">{{ __('Tanggal') }}</th>
                                    <th scope="col" class="text-end">{{ __('Jumlah') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($recentPayments as $payment)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $payment->invoice?->period_month }}/{{ $payment->invoice?->period_year }}</span>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $payment->invoice?->contract?->room?->roomType?->property?->name }}
                                        </td>
                                        <td class="text-muted small">
                                            {{ optional($payment->created_at)->format('d M Y') }}
                                        </td>
                                        <td class="text-end fw-semibold">
                                            Rp{{ number_format($payment->amount ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            {{ __('Belum ada pembayaran terbaru.') }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Aktivitas & Navigasi Cepat') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <a href="{{ route('tenant.wishlist.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-heart-fill me-1"></i>{{ __('Wishlist') }}
                            </a>
                            <a href="{{ route('tenant.tickets.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-ticket-perforated me-1"></i>{{ __('Tiket') }}
                            </a>
                            <a href="{{ route('chat.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-chat-dots me-1"></i>{{ __('Chat') }}
                            </a>
                        </div>
                        <div class="alert alert-primary mb-0">
                            <strong>{{ __('Butuh bantuan cepat?') }}</strong>
                            <div class="text-muted small mb-2">{{ __('Buat tiket atau buka chat untuk menyampaikan pertanyaanmu.') }}</div>
                            <a href="{{ route('tenant.tickets.create') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>{{ __('Buat Tiket') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" integrity="sha384-lcVYgnlV8PXkqsGg4uWclqkv0I2mxZEs8NVzssnQy9X9iSvbOfMu8MLzumoyrm21" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const paymentTrendCtx = document.getElementById('tenant-payment-trend');
            const paymentLabels = @json($paymentTrend['labels']);
            const paymentData = @json($paymentTrend['data']);

            if (paymentTrendCtx && paymentLabels.length) {
                new Chart(paymentTrendCtx, {
                    type: 'line',
                    data: {
                        labels: paymentLabels,
                        datasets: [{
                            label: '{{ __('Total Pembayaran (Rp)') }}',
                            data: paymentData,
                            tension: 0.35,
                            fill: true,
                            backgroundColor: 'rgba(13, 110, 253, 0.15)',
                            borderColor: 'rgba(13, 110, 253, 1)',
                            pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                            pointRadius: 4,
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

            const statusCtx = document.getElementById('tenant-invoice-status');
            const statusData = @json($invoiceStatusBreakdown);

            if (statusCtx && Object.keys(statusData).length) {
                const statusLabels = Object.keys(statusData).map(status => status.replaceAll('_', ' ').toUpperCase());
                const statusValues = Object.values(statusData);
                const statusColors = {
                    unpaid: '#ffc107',
                    overdue: '#dc3545',
                    paid: '#198754',
                    pending_verification: '#0d6efd',
                    canceled: '#6c757d',
                };

                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusValues,
                            backgroundColor: Object.keys(statusData).map(key => statusColors[key] ?? '#adb5bd'),
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
        });
    </script>
@endpush
