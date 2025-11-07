@extends('layouts.public')

@section('content')
<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="fw-semibold mb-3">{{ __('Syarat & Ketentuan KostIn') }}</h1>
                <p class="text-muted">
                    {{ __('Dokumen ini menjadi perjanjian hukum antara PT KostIn Digital Indonesia ("KostIn") dengan pengguna pemilik maupun penyewa. Dengan membuat akun, mengunggah properti, atau menekan tombol "Saya setuju" saat mengajukan kontrak, Anda menyatakan telah membaca, memahami, dan menerima seluruh ketentuan di bawah.') }}
                </p>

                <div class="alert alert-info">
                    <strong>{{ __('Ringkasan cepat:') }}</strong>
                    <ul class="mb-0 ps-3">
                        <li>{{ __('Semua data kontrak dan invoice bersifat elektronik dan memiliki kekuatan hukum.') }}</li>
                        <li>{{ __('Pembayaran dilakukan per transaksi invoice; tidak ada penagihan otomatis tanpa persetujuan tenant.') }}</li>
                        <li>{{ __('Setiap pengajuan kontrak otomatis terikat dengan User Agreement ini.') }}</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('1. Definisi Peran') }}</h5>
                    <p class="text-muted mb-1">{{ __('"Pemilik" adalah pihak yang menerbitkan listing dan mengelola kontrak. "Penyewa" adalah pihak yang mengajukan dan membayar kontrak. "Admin KostIn" bertindak sebagai penyelenggara platform.') }}</p>
                    <p class="text-muted mb-0">{{ __('Setiap akun wajib menggunakan identitas asli. KostIn berhak menonaktifkan akun yang meniru pihak lain atau melanggar hukum.') }}</p>
                </div>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('2. Pengajuan Kontrak & User Agreement') }}</h5>
                    <ul class="text-muted">
                        <li>{{ __('Saat tenant mengisi formulir pengajuan dan mencentang persetujuan, tenant menyetujui syarat sewa yang ditampilkan pada halaman properti, termasuk aturan rumah, jadwal pembayaran, dan kebijakan refund.') }}</li>
                        <li>{{ __('Pemilik berkewajiban memperbarui status pengajuan (disetujui/ditolak) melalui dashboard KostIn. Keputusan otomatis tercatat sebagai dokumen elektronik sah.') }}</li>
                        <li>{{ __('Tenant dapat mengunduh PDF kontrak kapan pun. Dokumen tersebut merupakan versi final dari kesepakatan digital ini.') }}</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('3. Invoice & Pembayaran') }}</h5>
                    <ul class="text-muted">
                        <li>{{ __('Invoice dibuat per transaksi sesuai pilihan tenant (jumlah bulan fleksibel). Sistem tidak akan membuat invoice baru sebelum invoice sebelumnya selesai dibayar/ditolak.') }}</li>
                        <li>{{ __('Pembayaran QRIS diproses oleh Midtrans. Status sukses/bermasalah mengikuti notifikasi resmi Midtrans.') }}</li>
                        <li>{{ __('Pembayaran manual wajib disertai bukti unggahan. Pemilik memiliki waktu maksimal 1x24 jam kerja untuk menyetujui atau menolak.') }}</li>
                        <li>{{ __('Biaya tambahan, denda, dan kebijakan refund diatur oleh pemilik dan akan tertulis di invoice/kontrak masing-masing.') }}</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('4. Dokumen Elektronik') }}</h5>
                    <p class="text-muted mb-1">{{ __('Kontrak dan invoice dalam bentuk PDF yang diunduh melalui KostIn dianggap setara dengan dokumen fisik. Penyimpanan versi final menjadi tanggung jawab masing-masing pihak.') }}</p>
                    <p class="text-muted mb-0">{{ __('KostIn menyimpan log aktivitas dan tanda waktu (timestamp) untuk setiap persetujuan sebagai bukti audit.') }}</p>
                </div>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('5. Pembatalan & Sengketa') }}</h5>
                    <ul class="text-muted">
                        <li>{{ __('Tenant dapat membatalkan pengajuan sebelum disetujui pemilik tanpa biaya. Setelah disetujui, pembatalan mengikuti kebijakan refund yang disepakati.') }}</li>
                        <li>{{ __('Sengketa pembayaran atau fasilitas diselesaikan langsung antara pemilik dan tenant. KostIn hanya menyediakan data dan rekaman bukti digital.') }}</li>
                        <li>{{ __('Jika diperlukan, KostIn dapat membantu memberikan log aktivitas sebagai bahan klarifikasi ke pihak berwenang.') }}</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('6. Batasan & Tanggung Jawab') }}</h5>
                    <p class="text-muted">{{ __('KostIn tidak bertanggung jawab atas kerusakan fisik, kehilangan, atau perselisihan di luar sistem. Tanggung jawab KostIn terbatas pada penyediaan platform sesuai standar keamanan aplikasi web modern.') }}</p>
                </div>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('7. Pembaruan Ketentuan') }}</h5>
                    <p class="text-muted mb-1">{{ __('Ketentuan ini dapat berubah sewaktu-waktu. Versi terbaru akan diumumkan melalui halaman ini dan pemberitahuan email.') }}</p>
                    <p class="text-muted mb-0">{{ __('Pengguna yang tetap memakai layanan setelah pembaruan dianggap menyetujui ketentuan terbaru.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
