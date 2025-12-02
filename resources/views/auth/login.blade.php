@extends('layouts.app')

@push('styles')
<style>
    .auth-surface {
        background: radial-gradient(95% 85% at 25% 20%, rgba(71, 138, 255, 0.18), transparent 50%),
            radial-gradient(85% 80% at 90% 0%, rgba(111, 201, 173, 0.18), transparent 45%),
            radial-gradient(75% 75% at 10% 90%, rgba(255, 193, 7, 0.16), transparent 50%),
            linear-gradient(135deg, #f8fafc, #ffffff);
    }

    .auth-card {
        border: 1px solid #edf1f5;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
    }

    .auth-pill {
        background: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        border-radius: 999px;
        padding: 0.35rem 0.9rem;
        font-weight: 600;
    }

    .auth-highlight {
        background: #ffffff;
        border-radius: 16px;
        border: 1px dashed #d7e3f4;
        padding: 1rem 1.25rem;
    }

    .feature-list .badge {
        width: 32px;
        height: 32px;
    }
</style>
@endpush

@section('content')
<div class="auth-surface py-5">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5 d-flex">
                <div class="bg-white border rounded-4 p-4 p-lg-5 w-100 shadow-sm">
                    <span class="auth-pill d-inline-flex align-items-center gap-2">
                        <i class="bi bi-lightning-charge-fill"></i> Mulai lebih cepat
                    </span>
                    <h1 class="mt-3 mb-3 fw-bold text-dark">
                        Masuk ke KostIn untuk lanjutkan perjalanan sewa kosmu
                    </h1>
                    <p class="text-secondary mb-4">
                        Pantau tagihan, kontrak, dan percakapan dengan pemilik dalam satu dashboard. Selesaikan pembayaran tepat waktu dengan notifikasi real-time.
                    </p>

                    <div class="auth-highlight mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-shield-check text-primary fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-semibold mb-1">Aman & terhubung</div>
                                <small class="text-secondary d-block">Login dengan Google untuk verifikasi cepat dan sesi yang lebih aman.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 feature-list">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-3">
                                <span class="badge bg-primary-subtle text-primary-emphasis rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <i class="bi bi-collection-play-fill"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold">Timeline tagihan</div>
                                    <small class="text-secondary">Semua pembayaran, bukti unggah, dan status Midtrans.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-3">
                                <span class="badge bg-success-subtle text-success-emphasis rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <i class="bi bi-chat-heart-fill"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold">Obrolan responsif</div>
                                    <small class="text-secondary">Notifikasi chat tanpa ketinggalan pesan penting.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-3">
                                <span class="badge bg-warning-subtle text-warning-emphasis rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold">Monitor kontrak</div>
                                    <small class="text-secondary">Lihat status aktif & perpanjangan dalam satu layar.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-3">
                                <span class="badge bg-danger-subtle text-danger-emphasis rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <i class="bi bi-shield-lock-fill"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold">Keamanan berlapis</div>
                                    <small class="text-secondary">Sesi terenkripsi dengan CSRF & proteksi brute force.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-xl-6 ms-auto">
                <div class="card auth-card rounded-4">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h2 class="mb-1">Masuk akun</h2>
                                <p class="mb-0 text-secondary">Lanjutkan ke dashboard tenant atau owner Anda.</p>
                            </div>
                            <span class="badge bg-primary-subtle text-primary-emphasis px-3 py-2">
                                <i class="bi bi-shield-lock me-1"></i> Keamanan login aktif
                            </span>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-warning" role="alert">
                                <strong>Ups!</strong> Terjadi kesalahan saat memproses data kamu.
                                <ul class="mb-0 mt-2 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <a href="{{ route('auth.redirect') }}" class="btn btn-outline-dark w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-google"></i>
                            Masuk dengan Google
                        </a>

                        <div class="d-flex align-items-center text-secondary mb-3">
                            <hr class="flex-grow-1 opacity-50">
                            <span class="px-2 small text-uppercase">atau</span>
                            <hr class="flex-grow-1 opacity-50">
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" required autofocus placeholder="nama@email.com">
                                </div>
                                @error('email')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-key"></i></span>
                                    <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-secondary">Gunakan minimal 8 karakter kombinasi huruf & angka.</small>
                                </div>
                                @error('password')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" name="remember" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <span class="fw-semibold">Masuk sekarang</span>
                            </button>
                        </form>

                        <p class="mt-4 mb-0 text-center text-secondary">
                            Belum punya akun?
                            <a href="{{ route('register') }}" class="fw-semibold">Daftar terlebih dahulu</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
