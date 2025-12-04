<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pengingat Tagihan</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            padding: 20px;
            border: 1px solid #eee;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Pengingat Tagihan Kost</h2>
        </div>
        <div class="content">
            <p>Halo {{ $invoice->contract->tenant->name }},</p>

            <p>Ini adalah pengingat bahwa tagihan sewa kost Anda akan segera jatuh tempo.</p>

            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <p style="margin: 0;">Total Tagihan:</p>
                <p class="amount">Rp {{ number_format($invoice->total, 0, ',', '.') }}</p>
                <p style="margin: 10px 0 0;">Jatuh Tempo: <strong>{{ $invoice->due_date->format('d M Y') }}</strong></p>
            </div>

            <p>Mohon segera lakukan pembayaran sebelum tanggal jatuh tempo untuk menghindari denda keterlambatan.</p>

            <div style="text-align: center;">
                <a href="{{ env('FRONTEND_URL') }}/tenant/invoices" class="button">Bayar Sekarang</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>