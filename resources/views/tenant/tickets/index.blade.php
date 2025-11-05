<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Tiket Saya') }}</h1>
            <small class="text-muted">{{ __('Pantau progress laporan dan permintaan bantuan Anda.') }}</small>
        </div>
        <a href="{{ route('tenant.tickets.create') }}" class="btn btn-primary btn-sm">
            {{ __('Buat Tiket Baru') }}
        </a>
    </x-slot>

    <div class="container-fluid">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
            </div>
        @endif

        <div class="row g-3 mb-4">
            @php
                $statusMap = [
                    \App\Models\Ticket::STATUS_OPEN => __('Open'),
                    \App\Models\Ticket::STATUS_IN_REVIEW => __('In Review'),
                    \App\Models\Ticket::STATUS_ESCALATED => __('Escalated'),
                    \App\Models\Ticket::STATUS_RESOLVED => __('Resolved'),
                    \App\Models\Ticket::STATUS_REJECTED => __('Rejected'),
                ];
                $badgeClasses = [
                    \App\Models\Ticket::STATUS_OPEN => 'primary',
                    \App\Models\Ticket::STATUS_IN_REVIEW => 'warning text-dark',
                    \App\Models\Ticket::STATUS_ESCALATED => 'danger',
                    \App\Models\Ticket::STATUS_RESOLVED => 'success',
                    \App\Models\Ticket::STATUS_REJECTED => 'secondary',
                ];
            @endphp
            @foreach ($statusMap as $status => $label)
                <div class="col-md-4 col-xl-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <p class="text-uppercase text-muted small mb-1">{{ $label }}</p>
                            <h3 class="mb-0">
                                {{ $statusCounts->get($status, 0) }}
                            </h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('Tiket') }}</th>
                            <th scope="col">{{ __('Kategori & Prioritas') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col">{{ __('Terakhir Diperbarui') }}</th>
                            <th scope="col" class="text-end">{{ __('Aksi') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($tickets as $ticket)
                            @php
                                $priorityLabels = [
                                    'low' => __('Rendah'),
                                    'medium' => __('Sedang'),
                                    'high' => __('Tinggi'),
                                    'urgent' => __('Mendesak'),
                                ];
                                $priorityClasses = [
                                    'low' => 'secondary',
                                    'medium' => 'info text-dark',
                                    'high' => 'warning text-dark',
                                    'urgent' => 'danger',
                                ];
                                $categoryLabels = [
                                    'technical' => __('Teknis'),
                                    'payment' => __('Pembayaran'),
                                    'content' => __('Konten'),
                                    'abuse' => __('Pelanggaran'),
                                ];
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold mb-1">{{ $ticket->subject }}</div>
                                    <div class="text-muted small">
                                        #{{ $ticket->ticket_code }} &middot; {{ $ticket->assignee?->name ?? __('Belum ditugaskan') }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark me-1">{{ $categoryLabels[$ticket->category] ?? ucfirst($ticket->category) }}</span>
                                    <span class="badge text-bg-{{ $priorityClasses[$ticket->priority] ?? 'secondary' }}">
                                        {{ $priorityLabels[$ticket->priority] ?? ucfirst($ticket->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge text-bg-{{ $badgeClasses[$ticket->status] ?? 'secondary' }}">
                                        {{ $statusMap[$ticket->status] ?? ucfirst($ticket->status) }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ optional($ticket->updated_at)->translatedFormat('d M Y H:i') ?? '—' }}
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('tenant.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                        {{ __('Detail') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    {{ __('Belum ada tiket dibuat. Laporkan kendala untuk mendapatkan bantuan.') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($tickets instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="card-footer bg-white">
                    {{ $tickets->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
