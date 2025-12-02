<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Tagihan Sewa') }}</h1>
            <small class="text-muted">{{ __('Lihat status, bayar QRIS, atau unggah bukti transfer dari satu tempat.') }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tenant.dashboard') }}" class="btn btn-light btn-sm">{{ __('Dashboard') }}</a>
        </div>
    </x-slot>

    @php
        $invoiceCollection = $invoices instanceof \Illuminate\Contracts\Pagination\Paginator ? $invoices->getCollection() : collect($invoices);
        $openCount = $invoiceCollection->whereIn('status', ['unpaid', 'overdue'])->count();
        $overdueCount = $invoiceCollection->where('status', 'overdue')->count();
        $totalOpen = $invoiceCollection->whereIn('status', ['unpaid', 'overdue'])->sum('total');
    @endphp

    <div class="container-fluid">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">{{ __('Tagihan Aktif') }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">{{ $openCount }}</h4>
                            <span class="badge bg-primary-subtle text-primary">{{ __('Diprioritaskan') }}</span>
                        </div>
                        <small class="text-muted">{{ __('Termasuk status unpaid & overdue.') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">{{ __('Overdue') }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 text-danger">{{ $overdueCount }}</h4>
                            <i class="bi bi-exclamation-triangle text-danger fs-5"></i>
                        </div>
                        <small class="text-muted">{{ __('Segera bayar agar tidak terkena denda.') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">{{ __('Total Belum Dibayar') }}</p>
                        <div class="h4 mb-0">Rp{{ number_format($totalOpen, 0, ',', '.') }}</div>
                        <small class="text-muted">{{ __('Perkiraan dari daftar ini.') }}</small>
                    </div>
                </div>
            </div>
        </div>

        @forelse ($invoices as $invoice)
            @php
                $property = $invoice->contract?->room?->roomType?->property;
                $statusClass = match ($invoice->status) {
                    'paid' => 'success',
                    'overdue' => 'danger',
                    'canceled' => 'secondary',
                    'pending_verification' => 'warning',
                    default => 'warning',
                };
                $statusLabel = str_replace('_', ' ', $invoice->status);
            @endphp
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge text-bg-{{ $statusClass }} text-capitalize">{{ $statusLabel }}</span>
                            @if ($invoice->status_reason)
                                <span class="badge bg-light text-dark">{{ $invoice->status_reason }}</span>
                            @endif
                        </div>
                        <h5 class="mb-1">{{ $property?->name ?? '—' }}</h5>
                        <div class="text-muted small">
                            {{ __('Periode') }} {{ $invoice->period_month }}/{{ $invoice->period_year }} · {{ __('Jatuh Tempo') }} {{ optional($invoice->due_date)->format('d M Y') ?? '—' }}
                        </div>
                        <div class="text-muted small">{{ __('Kamar') }} {{ $invoice->contract?->room?->room_code ?? '—' }} · {{ __('Total') }} Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="text-end d-flex flex-column gap-2">
                        <div class="fw-bold">Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</div>
                        <div class="d-flex gap-2 justify-content-end">
                            @if (in_array($invoice->status, ['unpaid', 'overdue'], true))
                                <a href="{{ route('tenant.invoices.show', $invoice) }}" class="btn btn-primary btn-sm">
                                    {{ __('Bayar Sekarang') }}
                                </a>
                            @endif
                            <a href="{{ route('tenant.invoices.show', $invoice) }}" class="btn btn-outline-primary btn-sm">
                                {{ __('Detail') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info border-0 shadow-sm">{{ __('Belum ada tagihan tersedia.') }}</div>
        @endforelse

        @if ($invoices instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="d-flex justify-content-end mt-3">
                {{ $invoices->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</x-app-layout>
