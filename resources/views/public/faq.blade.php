@extends('layouts.public')

@section('content')
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="fw-semibold">{{ __('Pertanyaan yang Sering Diajukan') }}</h1>
            <p class="text-muted">{{ __('Informasi singkat seputar onboarding, pembayaran, dan pengelolaan kontrak di KostIn.') }}</p>
        </div>

        <div class="accordion accordion-flush" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne" aria-expanded="false" aria-controls="faqCollapseOne">
                        {{ __('Bagaimana cara mendaftarkan kost?') }}
                    </button>
                </h2>
                <div id="faqCollapseOne" class="accordion-collapse collapse" aria-labelledby="faqHeadingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        {{ __('Daftar sebagai pemilik, lengkapi detail properti di portal owner. Tim admin akan meninjau sebelum publikasi.') }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo" aria-expanded="false" aria-controls="faqCollapseTwo">
                        {{ __('Bagaimana proses pengajuan kontrak oleh penyewa?') }}
                    </button>
                </h2>
                <div id="faqCollapseTwo" class="accordion-collapse collapse" aria-labelledby="faqHeadingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        {{ __('Penyewa memilih properti, mengisi formulir lengkap (kontak, pekerjaan, jumlah penghuni, dll.), lalu menyetujui user agreement. Pemilik meninjau dan menyetujui via dashboard KostIn, barulah kontrak aktif.') }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingPayment">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapsePayment" aria-expanded="false" aria-controls="faqCollapsePayment">
                        {{ __('Apakah invoice otomatis muncul setiap bulan?') }}
                    </button>
                </h2>
                <div id="faqCollapsePayment" class="accordion-collapse collapse" aria-labelledby="faqHeadingPayment" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        {{ __('Tidak. Invoice dibuat per transaksi sesuai kebutuhan tenant. Tenant dapat memilih berapa bulan yang ingin dibayar sekaligus, dan sistem hanya membuat invoice saat tombol “Buat Invoice” diklik.') }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingPDF">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapsePDF" aria-expanded="false" aria-controls="faqCollapsePDF">
                        {{ __('Bisakah kontrak dan invoice diunduh dalam bentuk PDF?') }}
                    </button>
                </h2>
                <div id="faqCollapsePDF" class="accordion-collapse collapse" aria-labelledby="faqHeadingPDF" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        {{ __('Ya. Tombol “Unduh PDF” tersedia di halaman detail kontrak maupun invoice. Dokumen hanya dibuat saat diminta, sehingga selalu menampilkan data terbaru.') }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingManualPay">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseManualPay" aria-expanded="false" aria-controls="faqCollapseManualPay">
                        {{ __('Bagaimana jika saya membayar secara manual?') }}
                    </button>
                </h2>
                <div id="faqCollapseManualPay" class="accordion-collapse collapse" aria-labelledby="faqHeadingManualPay" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        {{ __('Unggah bukti transfer pada halaman invoice. Pemilik wajib memverifikasi dalam 1x24 jam kerja. Status akan diperbarui otomatis setelah pemilik menyetujui.') }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingSecurity">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseSecurity" aria-expanded="false" aria-controls="faqCollapseSecurity">
                        {{ __('Apakah data saya aman?') }}
                    </button>
                </h2>
                <div id="faqCollapseSecurity" class="accordion-collapse collapse" aria-labelledby="faqHeadingSecurity" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        {{ __('KostIn menggunakan autentikasi berlapis, enkripsi pada data penting, serta pencatatan log aktivitas. Kami tidak menjual data pribadi ke pihak ketiga.') }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingSupport">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseSupport" aria-expanded="false" aria-controls="faqCollapseSupport">
                        {{ __('Siapa yang bisa membantu jika ada sengketa?') }}
                    </button>
                </h2>
                <div id="faqCollapseSupport" class="accordion-collapse collapse" aria-labelledby="faqHeadingSupport" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        {{ __('Selesaikan langsung dengan pemilik melalui fitur tiket atau chat di KostIn. Jika diperlukan, admin dapat memberi log aktivitas sebagai bukti tambahan.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
