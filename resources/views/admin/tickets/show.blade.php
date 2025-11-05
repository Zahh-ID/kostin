<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Detail Tiket') }}</h1>
            <small class="text-muted">{{ __('Kelola status, penugasan, dan catatan tiket.') }}</small>
        </div>
        <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary btn-sm">
            {{ __('Kembali') }}
        </a>
    </x-slot>

    <div class="container-fluid">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
            </div>
        @endif

        @php
            $statusMap = $statuses;
            $badgeClasses = [
                \App\Models\Ticket::STATUS_OPEN => 'primary',
                \App\Models\Ticket::STATUS_IN_REVIEW => 'warning text-dark',
                \App\Models\Ticket::STATUS_ESCALATED => 'danger',
                \App\Models\Ticket::STATUS_RESOLVED => 'success',
                \App\Models\Ticket::STATUS_REJECTED => 'secondary',
            ];
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

        <div class="row g-4">
            <div class="col-lg-5 col-xl-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h2 class="h5 mb-1">{{ $ticket->subject }}</h2>
                                <div class="text-muted small">
                                    #{{ $ticket->ticket_code }} &middot; {{ $categoryLabels[$ticket->category] ?? ucfirst($ticket->category) }}
                                </div>
                            </div>
                            <span class="badge text-bg-{{ $badgeClasses[$ticket->status] ?? 'secondary' }}">
                                {{ $statusMap[$ticket->status] ?? ucfirst($ticket->status) }}
                            </span>
                        </div>
                        <dl class="row small mb-0">
                            <dt class="col-5 text-muted">{{ __('Pelapor') }}</dt>
                            <dd class="col-7">{{ $ticket->reporter?->name ?? '—' }}</dd>
                            <dt class="col-5 text-muted">{{ __('Email') }}</dt>
                            <dd class="col-7">{{ $ticket->reporter?->email ?? '—' }}</dd>
                            <dt class="col-5 text-muted">{{ __('Prioritas') }}</dt>
                            <dd class="col-7">
                                <span class="badge text-bg-{{ $priorityClasses[$ticket->priority] ?? 'secondary' }}">
                                    {{ $priorityLabels[$ticket->priority] ?? ucfirst($ticket->priority) }}
                                </span>
                            </dd>
                            <dt class="col-5 text-muted">{{ __('Penugasan') }}</dt>
                            <dd class="col-7">{{ $ticket->assignee?->name ?? __('Belum ditugaskan') }}</dd>
                            <dt class="col-5 text-muted">{{ __('Dibuat') }}</dt>
                            <dd class="col-7">{{ optional($ticket->created_at)->translatedFormat('d M Y H:i') ?? '—' }}</dd>
                            <dt class="col-5 text-muted">{{ __('Diperbarui') }}</dt>
                            <dd class="col-7">{{ optional($ticket->updated_at)->translatedFormat('d M Y H:i') ?? '—' }}</dd>
                            @if ($ticket->closed_at)
                                <dt class="col-5 text-muted">{{ __('Ditutup') }}</dt>
                                <dd class="col-7">{{ optional($ticket->closed_at)->translatedFormat('d M Y H:i') ?? '—' }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <h3 class="h6 mb-0">{{ __('Perbarui Tiket') }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.tickets.update', $ticket) }}" method="post">
                            @csrf
                            @method('patch')
                            <div class="mb-3">
                                <label for="status" class="form-label">{{ __('Status') }}</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    @foreach ($statusMap as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $ticket->status) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="assignee_id" class="form-label">{{ __('Ditugaskan ke') }}</label>
                                <select name="assignee_id" id="assignee_id" class="form-select @error('assignee_id') is-invalid @enderror">
                                    <option value="">{{ __('Pilih penanggung jawab') }}</option>
                                    @foreach ($assignees as $assignee)
                                        <option value="{{ $assignee->id }}" @selected(old('assignee_id', $ticket->assignee_id) == $assignee->id)>
                                            {{ $assignee->name }} ({{ ucfirst($assignee->role) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('assignee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">{{ __('Catatan internal / respon') }}</label>
                                <textarea name="comment" id="comment" rows="4" class="form-control @error('comment') is-invalid @enderror" placeholder="{{ __('Masukkan update terbaru atau instruksi bagi tenant/pemilik...') }}">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('Catatan ini akan muncul pada percakapan tiket.') }}</div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Simpan Perubahan') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h3 class="h6 mb-0">{{ __('Riwayat Status') }}</h3>
                    </div>
                    <div class="card-body">
                        @forelse ($ticket->events as $event)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-capitalize">{{ str_replace('_', ' ', $event->event_type) }}</strong>
                                    <span class="text-muted small">{{ optional($event->created_at)->diffForHumans() }}</span>
                                </div>
                                @if (! empty($event->payload['note']))
                                    <div class="text-muted small">{{ $event->payload['note'] }}</div>
                                @endif
                                @if (! empty($event->payload['from']) && ! empty($event->payload['to']))
                                    <div class="text-muted small">
                                        {{ __('Status: :from → :to', [
                                            'from' => $statusMap[$event->payload['from']] ?? ucfirst($event->payload['from']),
                                            'to' => $statusMap[$event->payload['to']] ?? ucfirst($event->payload['to']),
                                        ]) }}
                                    </div>
                                @endif
                                @if (! empty($event->payload['assignee_id']))
                                    <div class="text-muted small">
                                        {{ __('Penugasan diperbarui (ID: :id)', ['id' => $event->payload['assignee_id']]) }}
                                    </div>
                                @endif
                                <div class="text-muted small mt-1">
                                    {{ __('oleh') }} {{ $event->user?->name ?? __('Sistem') }}
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">{{ __('Belum ada riwayat status tersedia.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-xl-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <h3 class="h6 mb-0">{{ __('Ringkasan Masalah') }}</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $ticket->description }}</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h3 class="h6 mb-0">{{ __('Diskusi & Komentar') }}</h3>
                        <span class="badge bg-light text-dark">{{ $ticket->comments->count() }} {{ __('komentar') }}</span>
                    </div>
                    <div class="card-body">
                        @forelse ($ticket->comments as $comment)
                            <div class="mb-4 pb-4 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $comment->user?->name ?? __('Pengguna') }}</strong>
                                        <span class="text-muted small d-block">
                                            {{ optional($comment->created_at)->translatedFormat('d M Y H:i') ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                                <p class="mb-0 mt-2">{{ $comment->body }}</p>
                                @if (is_array($comment->attachments) && count($comment->attachments) > 0)
                                    <div class="mt-2">
                                        @foreach ($comment->attachments as $attachment)
                                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($attachment) }}" class="btn btn-sm btn-outline-secondary me-2" target="_blank" rel="noopener">
                                                {{ __('Lampiran') }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('Belum ada komentar pada tiket ini.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
