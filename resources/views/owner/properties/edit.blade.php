<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 text-dark mb-0">
            {{ __('Edit Property') }}
        </h2>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <a href="{{ route('owner.properties.show', $property) }}" class="btn btn-link px-0">
                        &larr; {{ __('Back to detail') }}
                    </a>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="alert alert-warning d-flex align-items-start">
                            <i data-feather="alert-triangle" class="me-2"></i>
                            <div>
                                <strong>{{ __('Perbarui dengan hati-hati') }}</strong>
                                <p class="mb-0 small text-muted">
                                    {{ __('Perubahan akan tersimpan langsung. Jika status saat ini :status, ajukan ulang moderasi agar perubahan dapat ditayangkan.', ['status' => ucfirst($property->status)]) }}
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

                        <form method="POST" action="{{ route('owner.properties.update', $property) }}" class="row g-4">
                            @csrf
                            @method('PUT')

                            <div class="col-12">
                                <label for="name" class="form-label">{{ __('Property Name') }}</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name', $property->name) }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    required
                                >
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
                                    required
                                >{{ old('address', $property->address) }}</textarea>
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
                                    value="{{ old('lat', $property->lat) }}"
                                    class="form-control @error('lat') is-invalid @enderror"
                                >
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
                                    value="{{ old('lng', $property->lng) }}"
                                    class="form-control @error('lng') is-invalid @enderror"
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
                                >{{ old('rules_text', $property->rules_text) }}</textarea>
                                @error('rules_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a href="{{ route('owner.properties.show', $property) }}" class="btn btn-outline-secondary">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Save Changes') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
