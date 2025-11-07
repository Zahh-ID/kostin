<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2933; }
        h1, h2, h3, h4 { margin: 0; }
        .header { text-align: center; margin-bottom: 16px; }
        .section { margin-bottom: 16px; }
        .section-title { font-weight: bold; text-transform: uppercase; font-size: 12px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 6px 8px; vertical-align: top; }
        .table-border th, .table-border td { border: 1px solid #d1d5db; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice #{{ sprintf('INV-%04d', $invoice->id) }}</h1>
        <p class="muted">{{ $property?->name }} &middot; {{ $property?->address }}</p>
        <p>{{ __('Periode :month/:year', ['month' => $invoice->period_month, 'year' => $invoice->period_year]) }}</p>
    </div>

    <div class="section">
        <div class="section-title">{{ __('Informasi Pihak') }}</div>
        <table class="table-border">
            <tr>
                <th style="width: 35%;">{{ __('Penyewa') }}</th>
                <td>
                    {{ $tenant->name }}<br>
                    {{ $tenant->email }}<br>
                    {{ $tenant->profile?->phone ?? '-' }}
                </td>
            </tr>
            <tr>
                <th>{{ __('Properti & Kamar') }}</th>
                <td>
                    {{ $property?->name }}<br>
                    {{ __('Kamar') }} {{ $invoice->contract?->room?->room_code ?? '-' }} ({{ $invoice->contract?->room?->roomType?->name ?? '-' }})
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">{{ __('Detail Tagihan') }}</div>
        <table class="table-border">
            <tr>
                <th style="width: 35%;">{{ __('Jatuh Tempo') }}</th>
                <td>{{ optional($invoice->due_date)->translatedFormat('d F Y') ?? '—' }}</td>
            </tr>
            <tr>
                <th>{{ __('Nominal') }}</th>
                <td>Rp{{ number_format($invoice->amount ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>{{ __('Denda') }}</th>
                <td>Rp{{ number_format($invoice->late_fee ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>{{ __('Total') }}</th>
                <td><strong>Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <th>{{ __('Periode Dicakup') }}</th>
                <td>
                    {{ $invoice->months_count ?? 1 }} {{ __('bulan') }}
                    @if ($invoice->coverage_start_month && $invoice->coverage_end_month)
                        ({{ \Illuminate\Support\Carbon::create($invoice->coverage_start_year, $invoice->coverage_start_month, 1)->translatedFormat('M Y') }}
                        -
                        {{ \Illuminate\Support\Carbon::create($invoice->coverage_end_year, $invoice->coverage_end_month, 1)->translatedFormat('M Y') }})
                    @endif
                </td>
            </tr>
            <tr>
                <th>{{ __('Status') }}</th>
                <td>{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">{{ __('Riwayat Pembayaran') }}</div>
        <table class="table-border">
            <thead>
                <tr>
                    <th>{{ __('Tanggal') }}</th>
                    <th>{{ __('Channel') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Jumlah') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoice->payments as $payment)
                    <tr>
                        <td>{{ optional($payment->created_at)->translatedFormat('d M Y H:i') }}</td>
                        <td>{{ strtoupper($payment->payment_type) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->status)) }}</td>
                        <td>Rp{{ number_format($payment->amount ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">{{ __('Belum ada pembayaran yang tercatat.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">{{ __('Catatan Pembayaran') }}</div>
        <p class="muted">
            {{ __('Harap melakukan pembayaran tepat waktu sesuai jatuh tempo. Setelah melakukan pembayaran manual, unggah bukti pada portal tenant untuk mempercepat verifikasi.') }}
        </p>
    </div>
</body>
</html>
