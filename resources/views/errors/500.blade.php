@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <h1 class="display-4 fw-bold text-danger">500</h1>
            <p class="lead">Terjadi kesalahan pada server.</p>
            <p class="text-muted">Kami sedang mencoba memperbaiki masalah ini. Coba beberapa saat lagi atau hubungi tim support jika masalah berlanjut.</p>
            <a href="{{ route('home') }}" class="btn btn-outline-primary">Kembali ke Beranda</a>
        </div>
    </div>
</div>
@endsection
