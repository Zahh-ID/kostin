@extends('layouts.public')

@section('content')
<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="fw-semibold mb-3">{{ __('Kebijakan Privasi') }}</h1>
                <p class="text-muted">
                    {{ __('Kami menghargai privasi Anda. Kebijakan ini menjelaskan bagaimana KostIn mengelola data pribadi yang dikumpulkan dari pemilik dan penyewa.') }}
                </p>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('Data yang Dikumpulkan') }}</h5>
                    <ul class="text-muted">
                        <li>{{ __('Informasi akun seperti nama, email, dan nomor telepon.') }}</li>
                        <li>{{ __('Detail properti, kamar, kontrak, dan pembayaran yang Anda kelola.') }}</li>
                        <li>{{ __('Log aktivitas untuk kebutuhan audit dan keamanan.') }}</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('Penggunaan Data') }}</h5>
                    <p class="text-muted">
                        {{ __('Data digunakan untuk menjalankan fitur sistem, termasuk penjadwalan tagihan, pembuatan laporan, dan pengiriman notifikasi otomatis.') }}
                    </p>
                </div>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('Perlindungan Data') }}</h5>
                    <p class="text-muted">
                        {{ __('Kami memakai autentikasi Laravel, hashing password, dan otorisasi berbasis peran. Akses admin dicatat melalui audit log.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
