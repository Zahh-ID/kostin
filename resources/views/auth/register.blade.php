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
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border-radius: 999px;
        padding: 0.35rem 0.9rem;
        font-weight: 600;
    }

    .step-dot {
        width: 44px;
        height: 44px;
    }
    .role-option {
        border: 1px solid #e8eef5;
        border-radius: 14px;
        padding: 0.85rem 1rem;
        transition: all 0.2s ease;
    }
    .role-option:hover {
        border-color: #198754;
        box-shadow: 0 10px 24px rgba(25, 135, 84, 0.08);
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
                        <i class="bi bi-stars"></i> Buat akun baru
                    </span>
                    <h1 class="mt-3 mb-3 fw-bold text-dark">Gabung dengan KostIn</h1>
                    <p class="text-secondary mb-4">
                        Registrasi kurang dari 2 menit. Kelola kos, kontrak, tagihan, dan chat dalam satu platform yang terintegrasi.
                    </p>

                    <div class="bg-success-subtle text-success-emphasis rounded-4 p-3 mb-3 d-flex align-items-center gap-3">
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center step-dot">
                            <i class="bi bi-rocket-takeoff-fill"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Tentukan peranmu</div>
                            <small class="d-block">Pilih sebagai tenant atau owner untuk pengalaman dashboard yang disesuaikan.</small>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="badge bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center step-dot">
                                <i class="bi bi-clipboard-check-fill"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Alur onboarding jelas</div>
                                <small class="text-secondary">Konfirmasi email otomatis, info kontrak, dan akses chat langsung aktif.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="badge bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center step-dot">
                                <i class="bi bi-bounding-box-circles"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Integrasi pembayaran</div>
                                <small class="text-secondary">Midtrans QRIS siap pakai dan unggah manual untuk bukti pembayaran.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="badge bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center step-dot">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Kolaborasi tanpa ribet</div>
                                <small class="text-secondary">Tiketing dan chat terhubung langsung dengan pemilik maupun tenant.</small>
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
                                <h2 class="mb-1">Daftar akun baru</h2>
                                <p class="mb-0 text-secondary">Mulai kelola kos atau cari sewa favoritmu.</p>
                            </div>
                            <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">
                                <i class="bi bi-circle-fill me-1 small"></i> Langkah 1 dari 1
                            </span>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-warning" role="alert">
                                <strong>Ups!</strong> Ada beberapa data yang perlu diperiksa kembali.
                                <ul class="mb-0 mt-2 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <a href="{{ route('auth.redirect') }}" class="btn btn-outline-dark w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-google"></i>
                            Daftar dengan Google
                        </a>

                        <div class="d-flex align-items-center text-secondary mb-3">
                            <hr class="flex-grow-1 opacity-50">
                            <span class="px-2 small text-uppercase">atau</span>
                            <hr class="flex-grow-1 opacity-50">
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-person-circle"></i></span>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required placeholder="Nama lengkap">
                                </div>
                                @error('name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" required placeholder="nama@email.com">
                                </div>
                                @error('email')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control" placeholder="08xxxxxxxxxx">
                                </div>
                                @error('phone')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label d-block">Daftar sebagai</label>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="d-flex align-items-center gap-3 role-option cursor-pointer">
                                            <input class="form-check-input mt-0" type="radio" name="role" value="tenant" {{ old('role', 'tenant') === 'tenant' ? 'checked' : '' }}>
                                            <div>
                                                <div class="fw-semibold">Tenant</div>
                                                <small class="text-secondary d-block">Cari & kelola kontrak sewa.</small>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="d-flex align-items-center gap-3 role-option cursor-pointer">
                                            <input class="form-check-input mt-0" type="radio" name="role" value="owner" {{ old('role') === 'owner' ? 'checked' : '' }}>
                                            <div>
                                                <div class="fw-semibold">Owner</div>
                                                <small class="text-secondary d-block">Kelola properti & tenant.</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @error('role')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-key"></i></span>
                                    <input type="password" name="password" id="password" class="form-control" required placeholder="Minimal 8 karakter">
                                </div>
                                <small class="text-secondary">Gunakan kombinasi huruf, angka, dan simbol untuk keamanan maksimum.</small>
                                @error('password')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-shield-lock"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Ulangi password">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <span class="fw-semibold">Buat akun sekarang</span>
                            </button>
                        </form>

                        <p class="mt-4 mb-0 text-center text-secondary">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="fw-semibold">Masuk di sini</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
