@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('owner.shared-tasks.index') }}" class="text-decoration-none small text-muted">&larr; Kembali</a>
    <div class="d-flex justify-content-between align-items-start mt-2 mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ $sharedTask->title }}</h1>
            <p class="text-muted mb-0">{{ $sharedTask->property?->name }}</p>
        </div>
        <a href="{{ route('owner.shared-task-logs.index', $sharedTask) }}" class="btn btn-outline-secondary btn-sm">Lihat Log</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h2 class="h6 fw-semibold mb-0">Detail Tugas</h2>
                </div>
                <div class="card-body">
                    <dl class="row small mb-0">
                        <dt class="col-sm-4 text-muted">Properti</dt>
                        <dd class="col-sm-8">{{ $sharedTask->property?->name }}</dd>
                        <dt class="col-sm-4 text-muted">Assignee</dt>
                        <dd class="col-sm-8">{{ $sharedTask->assignee?->name ?? 'Belum ditetapkan' }}</dd>
                        <dt class="col-sm-4 text-muted">Next Run</dt>
                        <dd class="col-sm-8">{{ optional($sharedTask->next_run_at)->format('d M Y H:i') ?? 'Fleksibel' }}</dd>
                        <dt class="col-sm-4 text-muted">Repeat</dt>
                        <dd class="col-sm-8"><code class="small">{{ $sharedTask->rrule ?? '-' }}</code></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h2 class="h6 fw-semibold mb-0">Deskripsi</h2>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">{{ $sharedTask->description ?: 'Belum ada deskripsi.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white">
            <h2 class="h6 fw-semibold mb-0">Log Terbaru</h2>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @forelse ($sharedTask->logs as $log)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="fw-semibold mb-1">Dikerjakan {{ optional($log->run_at)->format('d M Y H:i') }}</p>
                                <p class="small text-muted mb-0">{{ $log->note ?? 'Tidak ada catatan.' }}</p>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary">{{ $log->completedBy?->name ?? 'Sistem' }}</span>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Belum ada log.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
