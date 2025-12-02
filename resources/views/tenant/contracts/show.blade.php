<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 mb-0 text-dark">{{ __('Detail Kontrak #'.$contract->id) }}</h2>
            <small class="text-muted">{{ $contract->room->roomType->property->name ?? '-' }}</small>
        </div>
        <div class="d-flex gap-2 flex-wrap justify-content-end">
            @if ($primaryInvoice)
                <a href="{{ route('tenant.invoices.show', $primaryInvoice) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-credit-card me-1"></i>{{ __('Bayar Tagihan') }}
                </a>
            @endif
            <a href="{{ route('tenant.contracts.pdf', $contract) }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
                <i class="bi bi-file-earmark-arrow-down me-1"></i>{{ __('Unduh PDF') }}
            </a>
            <a href="{{ route('tenant.contracts.index') }}" class="btn btn-sm btn-light">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    @php
        $nextCoverageLabel = optional($nextCoverageStart)->translatedFormat('F Y');
        $statusMap = [
            'draft' => 'secondary',
            'submitted' => 'info',
            'active' => 'success',
            'pending_renewal' => 'warning',
            'terminated' => 'danger',
            'canceled' => 'dark',
            'expired' => 'secondary',
        ];
        $badge = $statusMap[$contract->status] ?? 'primary';
    @endphp

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
        @endif

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-{{ $badge }} text-uppercase">{{ $contract->status }}</span>
                        @if (! is_null($daysToEnd) && $daysToEnd <= 30)
                            <span class="badge bg-warning text-dark">{{ __('Berakhir :days hari lagi', ['days' => max($daysToEnd, 0)]) }}</span>
                        @endif
                    </div>
                    <div class="fw-semibold">{{ $contract->room->roomType->property->name ?? '-' }}</div>
                    <div class="text-muted small">{{ $contract->room->roomType->property->address ?? '-' }}</div>
                    <div class="text-muted small mt-1">
                        {{ __('Periode:') }} {{ optional($contract->start_date)->format('d M Y') }} - {{ optional($contract->end_date)->format('d M Y') ?? __('Berjalan') }}
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-semibold">{{ __('Kamar') }} {{ $contract->room->room_code ?? '-' }} · {{ $contract->room->roomType->name ?? '-' }}</div>
                    <div class="text-muted small">{{ __('Harga/bln') }}: Rp{{ number_format($contract->price_per_month ?? 0, 0, ',', '.') }}</div>
                    <div class="text-muted small">{{ __('Deposit') }}: Rp{{ number_format($contract->deposit_amount ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">{{ __('Termin Kontrak') }}</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-6 text-muted small">{{ __('Tanggal mulai') }}</dt>
                            <dd class="col-6">{{ optional($contract->start_date)->format('d M Y') }}</dd>
                            <dt class="col-6 text-muted small">{{ __('Tanggal berakhir') }}</dt>
                            <dd class="col-6">{{ optional($contract->end_date)->format('d M Y') ?? __('Berjalan') }}</dd>
                            <dt class="col-6 text-muted small">{{ __('Tanggal jatuh tempo') }}</dt>
                            <dd class="col-6">{{ __('Tgl') }} {{ $contract->billing_day }}</dd>
                            <dt class="col-6 text-muted small">{{ __('Grace days') }}</dt>
                            <dd class="col-6">{{ $contract->grace_days }} {{ __('hari') }}</dd>
                            <dt class="col-6 text-muted small">{{ __('Denda keterlambatan') }}</dt>
                            <dd class="col-6">Rp{{ number_format($contract->late_fee_per_day ?? 0, 0, ',', '.') }}/{{ __('hari') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Pembayaran Berikutnya') }}</h5>
                        <span class="badge bg-light text-dark">{{ $nextCoverageLabel ?? __('-') }}</span>
                    </div>
                    <div class="card-body">
                        @if ($contract->status === \App\Models\Contract::STATUS_ACTIVE)
                            <form method="POST" action="{{ route('tenant.contracts.invoices.store', $contract) }}" class="row g-3 align-items-end">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">{{ __('Jumlah Bulan') }}</label>
                                    <select name="months_count" class="form-select form-select-sm">
                                        @foreach (range(1, 12) as $monthOption)
                                            <option value="{{ $monthOption }}" @selected(old('months_count', 1) == $monthOption)>{{ $monthOption }} {{ __('bulan') }}</option>
                                        @endforeach
                                    </select>
                                    @error('months_count')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-8 text-muted small">
                                    {{ __('Invoice baru akan memperpanjang masa sewa sesuai jumlah bulan yang dipilih dan hanya dapat dibuat setelah invoice sebelumnya lunas.') }}
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-file-earmark-plus me-1"></i>{{ __('Buat Invoice') }}
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="text-muted small mb-0">{{ __('Kontrak tidak aktif. Pembuatan invoice dinonaktifkan.') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">{{ __('Pengakhiran Kontrak') }}</h5>
                    <small class="text-muted">{{ __('Ajukan terminasi jika berencana pindah sebelum kontrak berakhir.') }}</small>
                </div>
                @if ($canRequestTermination && (! $latestTerminationRequest || $latestTerminationRequest->status !== 'pending'))
                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#terminateModal">
                        <i class="bi bi-flag me-1"></i>{{ __('Ajukan Pengakhiran') }}
                    </button>
                @elseif (! $canRequestTermination)
                    <button class="btn btn-outline-secondary btn-sm" disabled>
                        {{ __('Ajukan Pengakhiran') }}
                    </button>
                @endif
            </div>
            <div class="card-body">
                @if ($terminationBlockedReason)
                    <div class="alert alert-warning mb-3">{{ $terminationBlockedReason }}</div>
                @endif

                @if ($latestTerminationRequest)
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ __('Tanggal diminta') }}:</strong> {{ optional($latestTerminationRequest->requested_end_date)->translatedFormat('d M Y') }}</p>
                            <p class="mb-1"><strong>{{ __('Status') }}:</strong> {{ ucfirst($latestTerminationRequest->status) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ __('Alasan') }}:</strong> {{ $latestTerminationRequest->reason ?: __('Tidak ada alasan tambahan.') }}</p>
                            @if ($latestTerminationRequest->owner_notes)
                                <p class="mb-0"><strong>{{ __('Catatan pemilik') }}:</strong> {{ $latestTerminationRequest->owner_notes }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-muted mb-0">{{ __('Belum ada permintaan pengakhiran untuk kontrak ini.') }}</p>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">{{ __('Riwayat Tagihan') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Periode') }}</th>
                                <th>{{ __('Jatuh Tempo') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th class="text-end">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contract->invoices as $invoice)
                                @php
                                    $invStatus = $invoice->status;
                                    $invBadge = $invStatus === 'paid' ? 'success' : ($invStatus === 'overdue' ? 'danger' : 'warning');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $invoice->months_count ?? 1 }} {{ __('bln') }}</div>
                                        <div class="text-muted small">
                                            {{ optional($invoice->coverage_start_month ? \Illuminate\Support\Carbon::create($invoice->coverage_start_year, $invoice->coverage_start_month, 1) : null)?->translatedFormat('M Y') }}
                                            -
                                            {{ optional($invoice->coverage_end_month ? \Illuminate\Support\Carbon::create($invoice->coverage_end_year, $invoice->coverage_end_month, 1) : null)?->translatedFormat('M Y') }}
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ optional($invoice->due_date)->format('d M Y') }}</td>
                                    <td><span class="badge bg-{{ $invBadge }}">{{ $invoice->status }}</span></td>
                                    <td>Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('tenant.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>{{ __('Detail') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">{{ __('Tidak ada invoice untuk kontrak ini.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<div class="modal fade" id="terminateModal" tabindex="-1" aria-labelledby="terminateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('tenant.contracts.termination.store', $contract) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="terminateModalLabel">{{ __('Ajukan Pengakhiran Kontrak') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Tanggal pindah / berhenti') }}</label>
                        <input type="date" class="form-control" name="requested_end_date" value="{{ old('requested_end_date') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Alasan (opsional)') }}</label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="{{ __('Ceritakan alasan pengakhiran') }}">{{ old('reason') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Kirim Permintaan') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
