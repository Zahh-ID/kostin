<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kontrak #{{ $contract->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2933; }
        h1, h2, h3, h4 { margin: 0; }
        .header { text-align: center; margin-bottom: 16px; }
        .section { margin-bottom: 18px; }
        .section-title { font-weight: bold; font-size: 13px; margin-bottom: 8px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 6px 8px; vertical-align: top; }
        .table-border th, .table-border td { border: 1px solid #d1d5db; }
        .muted { color: #6b7280; }
        .signature { height: 80px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $property->name ?? 'Kontrak Sewa' }}</h1>
        <p class="muted">{{ $property->address }}</p>
        <p>No. Kontrak: {{ sprintf('KST-%04d', $contract->id) }}</p>
    </div>

    <div class="section">
        <div class="section-title">Informasi Pihak</div>
        <table class="table-border">
            <tr>
                <th style="width: 35%;">Penyewa</th>
                <td>
                    {{ $tenant->name }}<br>
                    {{ $tenant->email }}<br>
                    {{ $contract->tenant?->profile?->phone ?? '-' }}
                </td>
            </tr>
            <tr>
                <th>Pemilik / Pengelola</th>
                <td>
                    {{ optional($owner)->name ?? optional($property->owner)->name ?? '-' }}<br>
                    {{ optional($owner)->email ?? optional($property->owner)->email ?? '-' }}<br>
                    {{ $property->contact_phone ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detail Kontrak</div>
        <table class="table-border">
            <tr>
                <th style="width: 35%;">Properti & Kamar</th>
                <td>
                    {{ $property->name }}<br>
                    {{ __('Kamar') }} {{ $contract->room?->room_code ?? '-' }} ({{ $contract->room?->roomType?->name ?? '-' }})
                </td>
            </tr>
            <tr>
                <th>Periode</th>
                <td>
                    {{ optional($contract->start_date)->translatedFormat('d F Y') }} -
                    {{ optional($contract->end_date)->translatedFormat('d F Y') ?? __('Berjalan') }}
                </td>
            </tr>
            <tr>
                <th>Tagihan Bulanan</th>
                <td>Rp{{ number_format($contract->price_per_month ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Deposit</th>
                <td>Rp{{ number_format($contract->deposit_amount ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Jatuh Tempo</th>
                <td>{{ __('Tanggal') }} {{ $contract->billing_day }} setiap bulan (grace {{ $contract->grace_days }} hari)</td>
            </tr>
            <tr>
                <th>Denda Keterlambatan</th>
                <td>Rp{{ number_format($contract->late_fee_per_day ?? 0, 0, ',', '.') }}/hari</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Ketentuan Umum</div>
        <p>{{ $property->rules_text ?: __('Belum ada ketentuan tertulis dari pemilik.') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Ringkasan Tagihan Terakhir</div>
        <table class="table-border">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contract->invoices->take(5) as $invoice)
                    <tr>
                        <td>{{ $invoice->period_month }}/{{ $invoice->period_year }}</td>
                        <td>{{ optional($invoice->due_date)->format('d M Y') }}</td>
                        <td>{{ ucfirst($invoice->status) }}</td>
                        <td>Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">{{ __('Belum ada invoice yang tercatat.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Persetujuan</div>
        <table class="table-border">
            <tr>
                <td class="signature" style="width: 50%;">
                    <div class="muted">Penyewa</div>
                    <div style="margin-top: 45px; border-top: 1px solid #d1d5db; width: 80%;">{{ $tenant->name }}</div>
                </td>
                <td class="signature" style="width: 50%;">
                    <div class="muted">Pemilik / Pengelola</div>
                    <div style="margin-top: 45px; border-top: 1px solid #d1d5db; width: 80%;">{{ optional($owner)->name ?? optional($property->owner)->name ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
