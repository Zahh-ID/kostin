<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h5 class="card-title mb-0">{{ __('Informasi Profil') }}</h5>
        <small class="text-muted">{{ __('Perbarui nama dan alamat email akun Anda.') }}</small>
    </div>
    <div class="card-body">
        @if ($saved)
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ __('Perubahan profil berhasil disimpan.') }}
                <button type="button" class="btn-close" wire:click="$set('saved', false)" aria-label="{{ __('Tutup') }}"></button>
            </div>
        @endif

        <form wire:submit.prevent="updateProfileInformation" class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">{{ __('Nama Lengkap') }}</label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" wire:model.defer="name" required autocomplete="name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">{{ __('Alamat Email') }}</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" wire:model.defer="email" required autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @php
                    $fortifySupportsVerification = class_exists(\Laravel\Fortify\Features::class)
                        && \Laravel\Fortify\Features::enabled(\Laravel\Fortify\Features::emailVerification());
                @endphp

                @if ($fortifySupportsVerification && ! $this->user->hasVerifiedEmail())
                    <div class="alert alert-warning mt-3" role="alert">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ __('Email belum terverifikasi.') }}</strong>
                                <p class="mb-0 small">{{ __('Kirim ulang tautan verifikasi untuk mengaktifkan alamat email ini.') }}</p>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-warning" wire:click="sendEmailVerification" wire:loading.attr="disabled">
                                {{ __('Kirim Ulang') }}
                            </button>
                        </div>
                        @if ($this->verificationLinkSent)
                            <div class="text-success small mt-2">
                                {{ __('Tautan verifikasi baru telah dikirim ke email Anda.') }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    {{ __('Simpan Perubahan') }}
                </button>
            </div>
        </form>
    </div>
</div>
