<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} | Selamat Datang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">{{ config('app.name') }}</a>
        <div class="d-flex">
            <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn-light text-primary">Daftar</a>
        </div>
    </div>
</nav>
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-5 fw-bold">Platform Kost Modern</h1>
                <p class="lead">Kelola properti, kontrak, invoice, dan pembayaran QRIS dalam satu aplikasi backend yang terdokumentasi dengan baik.</p>
                <div class="mt-4">
                    <a href="{{ url('/api/docs') }}" class="btn btn-primary btn-lg">Lihat API Docs</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg ms-2">Masuk</a>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <img src="https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=900&q=80" class="img-fluid rounded shadow" alt="Kost Illustration">
            </div>
        </div>
    </div>
</section>
<footer class="py-3 bg-white border-top">
    <div class="container text-center">
        <small>&copy; {{ date('Y') }} {{ config('app.name') }}. Dibangun dengan Laravel 11.</small>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
