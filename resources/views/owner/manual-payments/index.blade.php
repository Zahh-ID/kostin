@extends('layouts.app')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Verifikasi Pembayaran Manual') }}</h1>
            <p class="text-muted mb-0">{{ __('Tinjau bukti transfer tenant dan setujui atau tolak sesuai kebutuhan.') }}</p>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>{{ __('Invoice') }}</th>
                        <th>{{ __('Tenant') }}</th>
                        <th>{{ __('Metode') }}</th>
                        <th>{{ __('Jumlah') }}</th>
                        <th>{{ __('Dikirim') }}</th>
                        <th>{{ __('Bukti') }}</th>
                        <th class="text-end">{{ __('Aksi') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>
                                <div class="fw-semibold">#{{ $payment->invoice?->id }}</div>
                                <small class="text-muted">
                                    {{ $payment->invoice?->contract?->room?->roomType?->property?->name }}
                                </small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $payment->submitter?->name }}</div>
                                <small class="text-muted">{{ $payment->submitter?->email }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary">{{ $payment->manual_method }}</span>
                                @if ($payment->notes)
                                    <div class="text-muted small mt-1">{{ $payment->notes }}</div>
                                @endif
                            </td>
                            <td>Rp{{ number_format($payment->amount ?? 0, 0, ',', '.') }}</td>
                            <td>{{ optional($payment->created_at)->format('d M Y H:i') }}</td>
                            <td>
                                @if ($payment->proof_path)
                                    <a href="{{ Storage::disk('public')->url($payment->proof_path) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">
                                        {{ __('Lihat Bukti') }}
                                    </a>
                                @else
                                    <span class="text-muted small">{{ __('Tidak ada lampiran') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('owner.manual-payments.update', $payment) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('patch')
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('{{ __('Setujui pembayaran ini?') }}')">
                                        {{ __('Setujui') }}
                                    </button>
                                </form>

                                <button class="btn btn-outline-danger btn-sm ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#reject-form-{{ $payment->id }}" aria-expanded="false" aria-controls="reject-form-{{ $payment->id }}">
                                    {{ __('Tolak') }}
                                </button>

                                <div class="collapse mt-2" id="reject-form-{{ $payment->id }}">
                                    <form action="{{ route('owner.manual-payments.update', $payment) }}" method="post">
                                        @csrf
                                        @method('patch')
                                        <input type="hidden" name="action" value="reject">
                                        <div class="mb-2">
                                            <textarea name="rejection_reason" class="form-control form-control-sm" rows="2" placeholder="{{ __('Alasan penolakan *') }}" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger btn-sm">{{ __('Kirim Penolakan') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">{{ __('Tidak ada pembayaran manual yang menunggu verifikasi.') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($payments instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="card-footer bg-white">
                {{ $payments->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
