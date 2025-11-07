<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 text-dark mb-0">
            {{ __('Add New Property') }}
        </h2>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <a href="{{ route('owner.properties.index') }}" class="btn btn-link px-0">
                        &larr; {{ __('Back to property list') }}
                    </a>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="alert alert-info d-flex align-items-start">
                            <i data-feather="info" class="me-2"></i>
                            <div>
                                <strong>{{ __('Draft first, publish later') }}</strong>
                                <p class="mb-0 small text-muted">
                                    {{ __('Lengkapi informasi dasar properti. Setelah disimpan sebagai draft, ajukan moderasi agar tim admin dapat memverifikasi dan menayangkannya.') }}
                                </p>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>{{ __('Periksa kembali data kamu:') }}</strong>
                                <ul class="mb-0 mt-2 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('owner.properties.store') }}" class="row g-4">
                            @csrf

                            <div class="col-12">
                                <label for="name" class="form-label">{{ __('Property Name') }}</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="{{ __('Contoh: Kost Harmoni Timoho') }}"
                                    required
                                >
                                <div class="form-text text-muted">
                                    {{ __('Gunakan nama yang mudah dikenali tenant.') }}
                                </div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">{{ __('Address') }}</label>
                                <textarea
                                    id="address"
                                    name="address"
                                    rows="3"
                                    class="form-control @error('address') is-invalid @enderror"
                                    placeholder="{{ __('Alamat lengkap beserta patokan atau area sekitar') }}"
                                    required
                                >{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="lat" class="form-label">{{ __('Latitude (optional)') }}</label>
                                <input
                                    id="lat"
                                    name="lat"
                                    type="text"
                                    value="{{ old('lat') }}"
                                    class="form-control @error('lat') is-invalid @enderror"
                                    placeholder="-6.200000"
                                >
                                <div class="form-text text-muted">
                                    {{ __('Isi jika ingin menampilkan pin peta yang lebih presisi.') }}
                                </div>
                                @error('lat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="lng" class="form-label">{{ __('Longitude (optional)') }}</label>
                                <input
                                    id="lng"
                                    name="lng"
                                    type="text"
                                    value="{{ old('lng') }}"
                                    class="form-control @error('lng') is-invalid @enderror"
                                    placeholder="106.816666"
                                >
                                @error('lng')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="rules_text" class="form-label">{{ __('House Rules (optional)') }}</label>
                                <textarea
                                    id="rules_text"
                                    name="rules_text"
                                    rows="4"
                                    class="form-control @error('rules_text') is-invalid @enderror"
                                    placeholder="{{ __('Contoh: Tidak boleh merokok di kamar, jam tamu hingga pukul 22.00.') }}"
                                >{{ old('rules_text') }}</textarea>
                                @error('rules_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a href="{{ route('owner.properties.index') }}" class="btn btn-outline-secondary">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Save Draft') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
