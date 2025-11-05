@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('owner.shared-tasks.show', $sharedTask) }}" class="text-decoration-none small text-muted">&larr; Kembali</a>
    <h1 class="h4 fw-semibold mt-2 mb-4">Log Tugas: {{ $sharedTask->title }}</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Penanggung Jawab</th>
                        <th>Catatan</th>
                        <th>Bukti Foto</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ optional($log->run_at)->format('d M Y H:i') }}</td>
                            <td>{{ $log->completedBy?->name ?? 'Sistem' }}</td>
                            <td>{{ $log->note ?? '-' }}</td>
                            <td>
                                @if ($log->photo_url)
                                    <a href="{{ $log->photo_url }}" target="_blank" rel="noopener">Lihat Foto</a>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada log tercatat.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($logs instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="card-footer bg-white">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
