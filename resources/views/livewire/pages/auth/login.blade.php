<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public LoginForm $form;
    public bool $showPassword = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

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
                            <small class="text-secondary">Pembayaran, bukti unggah, dan status Midtrans.</small>
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
                            <small class="text-secondary">Status aktif & perpanjangan selalu terpantau.</small>
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
                            <small class="text-secondary">Sesi terenkripsi dan proteksi brute force.</small>
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

                @if (session('status'))
                    <div class="alert alert-info" role="alert">
                        {{ session('status') }}
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

                <form wire:submit="login" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                            <input wire:model="form.email" id="email" type="email" class="form-control @error('form.email') is-invalid @enderror" required autofocus autocomplete="username" placeholder="nama@email.com">
                        </div>
                        @error('form.email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-key"></i></span>
                            <input
                                wire:model="form.password"
                                id="password"
                                type="{{ $showPassword ? 'text' : 'password' }}"
                                class="form-control @error('form.password') is-invalid @enderror"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            >
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                wire:click="$toggle('showPassword')"
                                aria-label="Tampilkan atau sembunyikan password"
                            >
                                <i class="bi {{ $showPassword ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                            </button>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-secondary">Minimal 8 karakter kombinasi huruf & angka.</small>
                            @if (Route::has('password.request'))
                                <a class="small" href="{{ route('password.request') }}" wire:navigate>Lupa password?</a>
                            @endif
                        </div>
                        @error('form.password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input wire:model="form.remember" id="remember" type="checkbox" class="form-check-input" name="remember">
                        <label class="form-check-label" for="remember">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <span class="fw-semibold">Masuk sekarang</span>
                    </button>
                </form>

                <p class="mt-4 mb-0 text-center text-secondary">
                    Belum punya akun?
                    <a href="{{ route('register') }}" wire:navigate class="fw-semibold">Daftar terlebih dahulu</a>
                </p>
            </div>
        </div>
    </div>
</div>
