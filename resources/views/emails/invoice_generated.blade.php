@php($contract = $invoice->contract)
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Generated</title>
</head>
<body>
    <h1>Halo {{ $contract?->tenant?->name ?? 'Tenant' }}!</h1>
    <p>Tagihan baru telah dibuat untuk kontrak kamar {{ $contract?->room?->room_code }} di {{ $contract?->room?->roomType?->property?->name }}.</p>
    <ul>
        <li>Periode: {{ $invoice->period_month }}/{{ $invoice->period_year }}</li>
        <li>Jatuh Tempo: {{ optional($invoice->due_date)->format('d/m/Y') }}</li>
        <li>Nominal: Rp{{ number_format($invoice->amount, 0, ',', '.') }}</li>
    </ul>
    <p>Silakan lakukan pembayaran sebelum jatuh tempo. Terima kasih.</p>
</body>
</html>
