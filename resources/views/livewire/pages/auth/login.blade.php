<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

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

<div>
    @if (session('status'))
        <div class="alert alert-info" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('auth.redirect') }}" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
            <span>Masuk dengan Google</span>
        </a>
    </div>

    <div class="text-center text-muted text-uppercase small mb-4 d-flex align-items-center">
        <div class="flex-grow-1 border-top"></div>
        <span class="px-3">atau gunakan email</span>
        <div class="flex-grow-1 border-top"></div>
    </div>

    <form wire:submit="login" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input wire:model="form.email" id="email" type="email" class="form-control @error('form.email') is-invalid @enderror" required autofocus autocomplete="username">
            @error('form.email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input wire:model="form.password" id="password" type="password" class="form-control @error('form.password') is-invalid @enderror" required autocomplete="current-password">
            @error('form.password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-check mb-3">
            <input wire:model="form.remember" id="remember" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label" for="remember">
                Ingat saya
            </label>
        </div>

        <div class="d-flex flex-column gap-2">
            <button type="submit" class="btn btn-primary w-100">
                Masuk
            </button>
            <div class="d-flex justify-content-between">
                <a class="link-secondary small" href="{{ route('register') }}" wire:navigate>
                    Belum punya akun?
                </a>
                @if (Route::has('password.request'))
                    <a class="link-secondary small" href="{{ route('password.request') }}" wire:navigate>
                        Lupa password?
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>
