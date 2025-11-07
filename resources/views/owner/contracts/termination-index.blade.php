@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Permintaan Pengakhiran Kontrak') }}</h1>
            <p class="text-muted mb-0">{{ __('Tinjau pengajuan terminasi dari tenant sebelum kontrak berakhir.') }}</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Tenant') }}</th>
                            <th>{{ __('Properti & Kamar') }}</th>
                            <th>{{ __('Tanggal Diminta') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $request)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $request->tenant?->name }}</div>
                                    <div class="text-muted small">{{ $request->tenant?->email }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ optional($request->contract->room->roomType->property)->name }}</div>
                                    <div class="text-muted small">{{ __('Kamar') }} {{ $request->contract->room->room_code ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ optional($request->requested_end_date)->translatedFormat('d M Y') }}</div>
                                    <div class="text-muted small">{{ $request->reason ?: __('Tidak ada alasan tambahan') }}</div>
                                </td>
                                <td>
                                    <span class="badge text-uppercase {{ $request->status === 'pending' ? 'bg-warning' : ($request->status === 'approved' ? 'bg-success' : 'bg-secondary') }}">
                                        {{ $request->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if ($request->status === 'pending')
                                        <form method="POST" action="{{ route('owner.contract-terminations.update', $request) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="action" value="approve">
                                            <button class="btn btn-sm btn-success" type="submit">{{ __('Setujui') }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('owner.contract-terminations.update', $request) }}" class="d-inline ms-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="action" value="reject">
                                            <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('Tolak') }}</button>
                                        </form>
                                    @else
                                        <div class="text-muted small">
                                            {{ __('Diproses pada :date', ['date' => optional($request->resolved_at)->translatedFormat('d M Y H:i')]) }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">{{ __('Belum ada permintaan terminasi.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($requests instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="card-footer bg-white">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
