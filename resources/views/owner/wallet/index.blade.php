@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">{{ __('Saldo Tersedia') }}</p>
                    <h1 class="display-6 fw-semibold">Rp{{ number_format($wallet->balance, 0, ',', '.') }}</h1>
                    <p class="text-muted small mb-3">{{ __('Dana yang siap dicairkan dari pembayaran tenant melalui Midtrans.') }}</p>
                    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                        {{ __('Cairkan Saldo') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h2 class="h5 fw-semibold mb-3">{{ __('Riwayat Transaksi') }}</h2>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Tanggal') }}</th>
                                    <th>{{ __('Tipe') }}</th>
                                    <th>{{ __('Keterangan') }}</th>
                                    <th class="text-end">{{ __('Jumlah') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td>{{ optional($transaction->created_at)->format('d M Y H:i') }}</td>
                                    <td>
                                        <span class="badge {{ $transaction->type === 'credit' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->description ?? '-' }}</td>
                                    <td class="text-end {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }}Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">{{ __('Belum ada transaksi.') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($transactions instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="card-footer bg-white border-0">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('owner.wallet.withdraw') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawModalLabel">{{ __('Ajukan Pencairan') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Jumlah (Rp)') }}</label>
                        <input type="number" min="1" step="10000" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">{{ __('Saldo tersedia:') }} Rp{{ number_format($wallet->balance, 0, ',', '.') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Catatan (opsional)') }}</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="{{ __('Misal: Cairkan ke rekening BCA 123xxxx') }}">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Kirim Permintaan') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
