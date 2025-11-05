@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <h1 class="display-4 fw-bold text-warning">404</h1>
            <p class="lead">Halaman tidak ditemukan.</p>
            <p class="text-muted">Alamat yang Anda tuju tidak tersedia atau telah dipindahkan. Gunakan navigasi untuk kembali ke halaman utama.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke Beranda</a>
        </div>
    </div>
</div>
@endsection
