<div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <h5 class="card-title mb-0 text-danger">{{ __('Hapus Akun') }}</h5>
            <small class="text-muted">{{ __('Tindakan ini akan menghapus seluruh data secara permanen.') }}</small>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">
                {{ __('Setelah akun dihapus, seluruh data dan riwayat transaksi tidak dapat dikembalikan. Pastikan Anda telah menyimpan informasi penting sebelum melanjutkan.') }}
            </p>
            <button type="button" class="btn btn-outline-danger" wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                {{ __('Hapus Akun') }}
            </button>
        </div>
    </div>

    @if ($confirmingUserDeletion)
        <div class="modal-backdrop fade show"></div>
    @endif

    <div class="modal fade @if ($confirmingUserDeletion) show d-block @endif" tabindex="-1" role="dialog" @if ($confirmingUserDeletion) style="display: block;" @else style="display: none;" @endif>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">{{ __('Konfirmasi Penghapusan Akun') }}</h5>
                    <button type="button" class="btn-close" wire:click="cancelUserDeletion" aria-label="{{ __('Tutup') }}"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">
                        {{ __('Masukkan kata sandi untuk mengkonfirmasi penghapusan akun. Tindakan ini tidak dapat dibatalkan.') }}
                    </p>
                    <form wire:submit.prevent="deleteUser" class="mt-3">
                        <div class="mb-3">
                            <label for="delete_password" class="form-label">{{ __('Kata Sandi') }}</label>
                            <input id="delete_password" type="password" class="form-control @error('password') is-invalid @enderror" wire:model.defer="password" autofocus>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" wire:click="cancelUserDeletion">
                                {{ __('Batal') }}
                            </button>
                            <button type="submit" class="btn btn-danger" wire:loading.attr="disabled">
                                {{ __('Hapus Akun') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
