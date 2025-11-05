<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Tagihan Sewa') }}</h1>
            <small class="text-muted">{{ __('Kelola seluruh tagihan bulanan Anda di sini.') }}</small>
        </div>
    </x-slot>

    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('Periode') }}</th>
                            <th scope="col">{{ __('Properti') }}</th>
                            <th scope="col">{{ __('Jatuh Tempo') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col" class="text-end">{{ __('Total') }}</th>
                            <th scope="col" class="text-end">{{ __('Aksi') }}</th>
                        </tr>
                        </thead>
                        <tbody>
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
                            <tr>
                                <td class="fw-semibold">{{ $invoice->period_month }}/{{ $invoice->period_year }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $property?->name ?? '—' }}</div>
                                    <small class="text-muted">{{ __('Kamar :code', ['code' => $invoice->contract?->room?->room_code ?? '—']) }}</small>
                                </td>
                                <td class="text-muted">{{ optional($invoice->due_date)->format('d M Y') ?? '—' }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $statusClass }} text-capitalize">{{ $statusLabel }}</span>
                                </td>
                                <td class="text-end fw-semibold">Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('tenant.invoices.show', $invoice) }}" class="btn btn-outline-primary btn-sm">
                                        {{ __('Detail') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    {{ __('Belum ada tagihan tersedia.') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($invoices instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="card-footer bg-white">
                    {{ $invoices->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
