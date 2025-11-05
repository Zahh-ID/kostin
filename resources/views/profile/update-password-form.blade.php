<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h5 class="card-title mb-0">{{ __('Ubah Kata Sandi') }}</h5>
        <small class="text-muted">{{ __('Pastikan akun Anda menggunakan kata sandi yang kuat.') }}</small>
    </div>
    <div class="card-body">
        @if (session('status') === 'password-updated')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ __('Kata sandi berhasil diperbarui.') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
            </div>
        @endif

        <form wire:submit.prevent="updatePassword" class="row g-3">
            <div class="col-md-4">
                <label for="current_password" class="form-label">{{ __('Kata Sandi Saat Ini') }}</label>
                <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" wire:model.defer="current_password" autocomplete="current-password">
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="password" class="form-label">{{ __('Kata Sandi Baru') }}</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" wire:model.defer="password" autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="password_confirmation" class="form-label">{{ __('Konfirmasi Kata Sandi') }}</label>
                <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" wire:model.defer="password_confirmation" autocomplete="new-password">
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    {{ __('Simpan Kata Sandi') }}
                </button>
            </div>
        </form>
    </div>
</div>
