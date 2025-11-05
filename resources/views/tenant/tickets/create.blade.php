<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Buat Tiket Baru') }}</h1>
            <small class="text-muted">{{ __('Jelaskan kendala yang Anda alami agar tim kami bisa membantu.') }}</small>
        </div>
        <a href="{{ route('tenant.tickets.index') }}" class="btn btn-outline-secondary btn-sm">
            {{ __('Kembali ke daftar tiket') }}
        </a>
    </x-slot>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('tenant.tickets.store') }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label for="category" class="form-label">{{ __('Kategori') }}</label>
                                <select name="category" id="category" class="form-select @error('category') is-invalid @enderror">
                                    <option value="" disabled @selected(old('category') === null)>{{ __('Pilih kategori masalah') }}</option>
                                    @foreach ($categories as $value => $label)
                                        <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="priority" class="form-label">{{ __('Prioritas') }}</label>
                                <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror">
                                    <option value="" disabled @selected(old('priority') === null)>{{ __('Seberapa mendesak isu ini?') }}</option>
                                    @foreach ($priorities as $value => $label)
                                        <option value="{{ $value }}" @selected(old('priority') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">{{ __('Subjek') }}</label>
                                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror" placeholder="{{ __('Contoh: Verifikasi pembayaran bulan ini') }}">
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="description" class="form-label">{{ __('Deskripsi Lengkap') }}</label>
                                <textarea name="description" id="description" rows="6" class="form-control @error('description') is-invalid @enderror" placeholder="{{ __('Jelaskan detail masalah, waktu kejadian, dan informasi pendukung lainnya.') }}">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('tenant.tickets.index') }}" class="btn btn-outline-secondary">
                                    {{ __('Batal') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Kirim Tiket') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
