@extends('layouts.public')

@section('content')
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-7">
                <h1 class="fw-semibold">{{ __('Hubungi Kami') }}</h1>
                <p class="text-muted mb-4">
                    {{ __('Butuh demo atau memiliki pertanyaan? Kirim pesan melalui formulir berikut, tim kami akan merespons maksimal 1x24 jam kerja.') }}
                </p>
                <form class="row g-3">
                    <div class="col-12">
                        <label for="contactName" class="form-label">{{ __('Nama Lengkap') }}</label>
                        <input type="text" id="contactName" class="form-control" placeholder="Nama lengkap">
                    </div>
                    <div class="col-12">
                        <label for="contactEmail" class="form-label">{{ __('Email') }}</label>
                        <input type="email" id="contactEmail" class="form-control" placeholder="you@example.com">
                    </div>
                    <div class="col-12">
                        <label for="contactMessage" class="form-label">{{ __('Pesan') }}</label>
                        <textarea id="contactMessage" class="form-control" rows="4" placeholder="{{ __('Tulis kebutuhan atau pertanyaan Anda') }}"></textarea>
                    </div>
                    <div class="col-12 d-grid d-md-inline-flex gap-2">
                        <button type="submit" class="btn btn-primary">{{ __('Kirim Pesan') }}</button>
                        <a href="mailto:support@kostin.app" class="btn btn-outline-primary">{{ __('Email Langsung') }}</a>
                    </div>
                </form>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Informasi Kontak') }}</h5>
                        <ul class="list-unstyled text-muted mb-4">
                            <li><strong>Email:</strong> support@kostin.app</li>
                            <li><strong>Telepon:</strong> +62 812-0000-0000</li>
                            <li><strong>Lokasi:</strong> Remote-first, Indonesia</li>
                        </ul>
                        <h6 class="fw-semibold">{{ __('Jam Operasional') }}</h6>
                        <p class="text-muted mb-0">{{ __('Senin - Jumat, 09.00 - 18.00 WIB') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
