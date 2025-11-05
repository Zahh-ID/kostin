<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Tiket Penugasan') }}</h1>
            <small class="text-muted">{{ __('Kelola laporan penyewa yang ditugaskan kepada Anda.') }}</small>
        </div>
    </x-slot>

    <div class="container-fluid">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="get" class="row g-3 align-items-end">
                    <div class="col-md-4 col-lg-3">
                        <label for="status" class="form-label">{{ __('Status') }}</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">{{ __('Semua status') }}</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Terapkan') }}
                        </button>
                    </div>
                    <div class="col-md-auto">
                        <a href="{{ route('owner.tickets.index') }}" class="btn btn-outline-secondary">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('Tiket') }}</th>
                            <th scope="col">{{ __('Pelapor') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col" class="text-end">{{ __('Aksi') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($tickets as $ticket)
                            @php
                                $badgeClasses = [
                                    \App\Models\Ticket::STATUS_OPEN => 'primary',
                                    \App\Models\Ticket::STATUS_IN_REVIEW => 'warning text-dark',
                                    \App\Models\Ticket::STATUS_ESCALATED => 'danger',
                                    \App\Models\Ticket::STATUS_RESOLVED => 'success',
                                    \App\Models\Ticket::STATUS_REJECTED => 'secondary',
                                ];
                                $statusLabels = $statuses;
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold mb-1">{{ $ticket->subject }}</div>
                                    <div class="text-muted small">
                                        #{{ $ticket->ticket_code }} &middot; {{ ucfirst($ticket->priority) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $ticket->reporter?->name ?? '—' }}</div>
                                    <div class="text-muted small">{{ $ticket->reporter?->email }}</div>
                                </td>
                                <td>
                                    <span class="badge text-bg-{{ $badgeClasses[$ticket->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('owner.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                        {{ __('Detail') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    {{ __('Belum ada tiket yang ditugaskan.') }}
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
