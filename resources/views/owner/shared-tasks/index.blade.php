@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-1">Tugas Bersama</h1>
            <p class="text-muted mb-0">Jadwalkan dan monitor tugas operasional properti.</p>
        </div>
        <a href="{{ route('owner.shared-tasks.create') }}" class="btn btn-primary btn-sm">Buat Tugas</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Judul</th>
                        <th>Properti</th>
                        <th>Penanggung Jawab</th>
                        <th>Jadwal Berikut</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->property?->name }}</td>
                            <td>{{ $task->assignee?->name ?? '-' }}</td>
                            <td>{{ optional($task->next_run_at)->format('d M Y H:i') ?? 'Fleksibel' }}</td>
                            <td class="text-end">
                                <a href="{{ route('owner.shared-tasks.show', $task) }}" class="btn btn-outline-primary btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada tugas.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($tasks instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="card-footer bg-white">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
