@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <h1 class="display-4 fw-bold text-danger">403</h1>
            <p class="lead">Akses ditolak.</p>
            <p class="text-muted">Anda tidak memiliki izin untuk membuka halaman ini. Silakan kembali ke dashboard atau hubungi administrator jika Anda merasa ini adalah kesalahan.</p>
            <a href="{{ url()->previous() }}" class="btn btn-outline-primary">Kembali</a>
        </div>
    </div>
</div>
@endsection
