<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = User::ROLE_TENANT;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:tenant,owner'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="row g-4 align-items-stretch">
    <div class="col-lg-5 d-flex">
        <div class="bg-white border rounded-4 p-4 p-lg-5 w-100 shadow-sm">
            <span class="auth-pill d-inline-flex align-items-center gap-2" style="background: rgba(25, 135, 84, 0.12); color: #198754;">
                <i class="bi bi-stars"></i> Buat akun baru
            </span>
            <h1 class="mt-3 mb-3 fw-bold text-dark">Gabung dengan KostIn</h1>
            <p class="text-secondary mb-4">
                Registrasi kurang dari 2 menit. Kelola kos, kontrak, tagihan, dan chat dalam satu platform yang terintegrasi.
            </p>

            <div class="bg-success-subtle text-success-emphasis rounded-4 p-3 mb-3 d-flex align-items-center gap-3">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-rocket-takeoff-fill"></i>
                </div>
                <div>
                    <div class="fw-semibold">Tentukan peranmu</div>
                    <small class="d-block">Pilih sebagai tenant atau owner untuk pengalaman dashboard yang disesuaikan.</small>
                </div>
            </div>

            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="badge bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-clipboard-check-fill"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">Alur onboarding jelas</div>
                        <small class="text-secondary">Konfirmasi email otomatis, info kontrak, dan akses chat langsung aktif.</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="badge bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-bounding-box-circles"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">Integrasi pembayaran</div>
                        <small class="text-secondary">Midtrans QRIS siap pakai dan unggah manual untuk bukti pembayaran.</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="badge bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
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

                <a href="{{ route('auth.redirect') }}" class="btn btn-outline-dark w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-google"></i>
                    Daftar dengan Google
                </a>

                <div class="d-flex align-items-center text-secondary mb-3">
                    <hr class="flex-grow-1 opacity-50">
                    <span class="px-2 small text-uppercase">atau</span>
                    <hr class="flex-grow-1 opacity-50">
                </div>

                <form wire:submit="register" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-person-circle"></i></span>
                            <input wire:model="name" id="name" type="text" class="form-control @error('name') is-invalid @enderror" required autofocus autocomplete="name" placeholder="Nama lengkap">
                        </div>
                        @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                            <input wire:model="email" id="email" type="email" class="form-control @error('email') is-invalid @enderror" required autocomplete="username" placeholder="nama@email.com">
                        </div>
                        @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-telephone"></i></span>
                            <input wire:model="phone" id="phone" type="text" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>
                        @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Daftar sebagai</label>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="d-flex align-items-center gap-3 role-option cursor-pointer">
                                    <input name="role" wire:model="role" class="form-check-input mt-0 @error('role') is-invalid @enderror" type="radio" value="tenant" checked>
                                    <div>
                                        <div class="fw-semibold">Tenant</div>
                                        <small class="text-secondary d-block">Cari & kelola kontrak sewa.</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="d-flex align-items-center gap-3 role-option cursor-pointer">
                                    <input name="role" wire:model="role" class="form-check-input mt-0 @error('role') is-invalid @enderror" type="radio" value="owner">
                                    <div>
                                        <div class="fw-semibold">Owner</div>
                                        <small class="text-secondary d-block">Kelola properti & tenant.</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @error('role') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-key"></i></span>
                            <input wire:model="password" id="password" type="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                        </div>
                        <small class="text-secondary">Gunakan kombinasi huruf, angka, dan simbol untuk keamanan maksimum.</small>
                        @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-shield-lock"></i></span>
                            <input wire:model="password_confirmation" id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" required autocomplete="new-password" placeholder="Ulangi password">
                        </div>
                        @error('password_confirmation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <span class="fw-semibold">Buat akun sekarang</span>
                    </button>
                </form>

                <p class="mt-4 mb-0 text-center text-secondary">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" wire:navigate class="fw-semibold">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
