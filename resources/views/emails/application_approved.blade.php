<!DOCTYPE html>
<html>

<head>
    <title>Pengajuan Sewa Disetujui</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;">
        <h2 style="color: #D67C06;">Selamat! Pengajuan Sewa Anda Disetujui</h2>

        <p>Halo <strong>{{ $application->tenant->name }}</strong>,</p>

        <p>Kabar gembira! Pengajuan sewa Anda untuk properti berikut telah disetujui oleh pemilik:</p>

        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 5px 0;"><strong>Properti:</strong> {{ $application->property->name }}</p>
            <p style="margin: 5px 0;"><strong>Kamar:</strong> {{ $application->room->room_code }}
                ({{ $application->roomType->name }})</p>
            <p style="margin: 5px 0;"><strong>Alamat:</strong> {{ $application->property->address }}</p>
            <p style="margin: 5px 0;"><strong>Durasi:</strong> {{ $application->duration_months }} Bulan</p>
            <p style="margin: 5px 0;"><strong>Mulai Tanggal:</strong>
                {{ $application->preferred_start_date->format('d M Y') }}</p>
        </div>

        <p>Langkah selanjutnya:</p>
        <ol>
            <li>Login ke akun Kostin Anda.</li>
            <li>Buka menu <strong>Kontrak Saya</strong>.</li>
            <li>Review dan tanda tangani kontrak sewa digital.</li>
            <li>Lakukan pembayaran tagihan pertama.</li>
        </ol>

        <p>Jika Anda memiliki pertanyaan, silakan hubungi pemilik kost melalui detail kontak yang tersedia di dashboard.
        </p>

        <p style="margin-top: 30px; font-size: 12px; color: #777;">
            Terima kasih telah menggunakan Kostin.<br>
            Ini adalah email otomatis, mohon tidak membalas email ini.
        </p>
    </div>
</body>

</html>