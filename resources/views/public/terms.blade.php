@extends('layouts.public')

@section('content')
<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="fw-semibold mb-3">{{ __('Syarat & Ketentuan') }}</h1>
                <p class="text-muted">
                    {{ __('Dengan menggunakan platform KostIn, Anda menyetujui ketentuan berikut. Harap baca dengan saksama sebelum melanjutkan.') }}
                </p>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('1. Akun & Keamanan') }}</h5>
                    <p class="text-muted">
                        {{ __('Pemilik dan penyewa bertanggung jawab menjaga kerahasiaan kredensial. Perubahan peran hanya dapat dilakukan oleh admin.') }}
                    </p>
                </div>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('2. Konten & Data') }}</h5>
                    <p class="text-muted">
                        {{ __('Data properti, foto, dan dokumen kontrak harus akurat. KostIn berhak menonaktifkan konten yang melanggar ketentuan.') }}
                    </p>
                </div>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('3. Pembayaran') }}</h5>
                    <p class="text-muted">
                        {{ __('Pembayaran diproses melalui Midtrans. Sengketa transaksi diselesaikan langsung antara pemilik dan penyewa.') }}
                    </p>
                </div>

                <div class="mt-4">
                    <h5 class="fw-semibold">{{ __('4. Batasan Tanggung Jawab') }}</h5>
                    <p class="text-muted">
                        {{ __('KostIn menyediakan sistem manajemen dan tidak bertanggung jawab atas kerugian akibat kelalaian penggunaan.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
