<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Ticketing & Moderasi') }}</h1>
            <small class="text-muted">{{ __('Pantau dan tindak lanjuti tiket dari penyewa maupun pemilik.') }}</small>
        </div>
    </x-slot>

    <div class="container-fluid">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
            </div>
        @endif

        @php
            $orderedStatuses = [
                \App\Models\Ticket::STATUS_OPEN,
                \App\Models\Ticket::STATUS_IN_REVIEW,
                \App\Models\Ticket::STATUS_ESCALATED,
                \App\Models\Ticket::STATUS_RESOLVED,
                \App\Models\Ticket::STATUS_REJECTED,
            ];
            $badgeClasses = [
                \App\Models\Ticket::STATUS_OPEN => 'primary',
                \App\Models\Ticket::STATUS_IN_REVIEW => 'warning text-dark',
                \App\Models\Ticket::STATUS_ESCALATED => 'danger',
                \App\Models\Ticket::STATUS_RESOLVED => 'success',
                \App\Models\Ticket::STATUS_REJECTED => 'secondary',
            ];
            $priorityClasses = [
                'low' => 'secondary',
                'medium' => 'info text-dark',
                'high' => 'warning text-dark',
                'urgent' => 'danger',
            ];
        @endphp

        <div class="row g-3 kanban-board">
            @foreach ($orderedStatuses as $status)
                @php
                    $statusTickets = $tickets->get($status, collect());
                @endphp
                <div class="col-12 col-md-6 col-xl">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                            <div class="fw-semibold">
                                {{ $statuses[$status] ?? ucfirst($status) }}
                            </div>
                            <span class="badge text-bg-{{ $badgeClasses[$status] ?? 'secondary' }}">{{ $statusTickets->count() }}</span>
                        </div>
                        <div class="card-body">
                            @forelse ($statusTickets as $ticket)
                                <div class="border rounded-3 p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <div class="fw-semibold">{{ $ticket->subject }}</div>
                                            <div class="text-muted small">
                                                #{{ $ticket->ticket_code }} &middot; {{ $ticket->reporter?->name ?? __('Tanpa nama') }}
                                            </div>
                                        </div>
                                        <span class="badge text-bg-{{ $priorityClasses[$ticket->priority] ?? 'secondary' }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </div>
                                    <div class="text-muted small mt-2">
                                        {{ __('Ditugaskan ke') }} {{ $ticket->assignee?->name ?? __('Belum ditugaskan') }}
                                    </div>
                                    <div class="mt-3 d-flex gap-2">
                                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                            {{ __('Kelola') }}
                                        </a>
                                        <form action="{{ route('admin.tickets.update', $ticket) }}" method="post" class="d-flex gap-2 align-items-center">
                                            @csrf
                                            @method('patch')
                                            <select name="status" class="form-select form-select-sm">
                                                @foreach ($statuses as $value => $label)
                                                    <option value="{{ $value }}" @selected($ticket->status === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="assignee_id" value="{{ $ticket->assignee_id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                {{ __('Update') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">
                                    {{ __('Tidak ada tiket pada status ini.') }}
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
