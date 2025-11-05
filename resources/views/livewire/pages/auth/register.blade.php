<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
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

<div>
    <div class="mb-4">
        <a href="{{ route('auth.redirect') }}" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
            <span>Daftar dengan Google</span>
        </a>
    </div>

    <div class="text-center text-muted text-uppercase small mb-4 d-flex align-items-center">
        <div class="flex-grow-1 border-top"></div>
        <span class="px-3">atau daftar dengan email</span>
        <div class="flex-grow-1 border-top"></div>
    </div>

    <form wire:submit="register" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input wire:model="name" id="name" type="text" class="form-control @error('name') is-invalid @enderror" required autofocus autocomplete="name">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input wire:model="email" id="email" type="email" class="form-control @error('email') is-invalid @enderror" required autocomplete="username">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label d-block">Daftar sebagai</label>
            <div class="form-check form-check-inline">
                <input wire:model="role" class="form-check-input @error('role') is-invalid @enderror" type="radio" id="roleTenant" value="tenant" checked>
                <label class="form-check-label" for="roleTenant">Tenant (Penyewa)</label>
            </div>
            <div class="form-check form-check-inline">
                <input wire:model="role" class="form-check-input @error('role') is-invalid @enderror" type="radio" id="roleOwner" value="owner">
                <label class="form-check-label" for="roleOwner">Owner (Pemilik)</label>
            </div>
            @error('role') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input wire:model="password" id="password" type="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" required autocomplete="new-password">
            @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Buat Akun
        </button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" wire:navigate class="link-primary">Sudah punya akun? Masuk</a>
    </div>
</div>
