@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('owner.room-types.index') }}" class="text-decoration-none small text-muted">&larr; {{ __('Kembali') }}</a>
    <h1 class="h4 fw-semibold mt-2 mb-4">{{ __('Tambah Tipe Kamar') }}</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            @php
                $defaultPropertyId = old('property_id', $selectedProperty->id ?? optional($properties->first())->id);
            @endphp
            <form method="POST" action="{{ $defaultPropertyId ? route('owner.properties.room-types.store', $defaultPropertyId) : '#' }}" class="row g-3" id="room-type-form">
                @csrf
                @if (! empty($selectedProperty))
                    <input type="hidden" name="property_id" value="{{ $selectedProperty->id }}">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small mb-1">{{ __('Properti dipilih') }}</p>
                                <h5 class="fw-semibold mb-0">{{ $selectedProperty->name }}</h5>
                                <p class="text-muted small mb-0">{{ $selectedProperty->address }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Properti') }}</label>
                        <select class="form-select" name="property_id" required data-action-base="{{ url('owner/properties') }}">
                            <option value="">{{ __('Pilih Properti') }}</option>
                            @forelse ($properties as $property)
                                <option value="{{ $property->id }}" @selected($defaultPropertyId == $property->id)>{{ $property->name }}</option>
                            @empty
                                <option value="" disabled>{{ __('Belum ada properti') }}</option>
                            @endforelse
                        </select>
                        @error('property_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                @endif
                <div class="col-md-6">
                    <label class="form-label">{{ __('Nama Tipe') }}</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Luas (m²)') }}</label>
                    <input type="number" class="form-control" name="area_m2" value="{{ old('area_m2') }}">
                    @error('area_m2')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Tipe Kamar Mandi') }}</label>
                    <select class="form-select" name="bathroom_type" required>
                        <option value="inside" @selected(old('bathroom_type') === 'inside')>{{ __('Dalam') }}</option>
                        <option value="outside" @selected(old('bathroom_type') === 'outside')>{{ __('Luar') }}</option>
                    </select>
                    @error('bathroom_type')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Harga Dasar / Bulan (Rp)') }}</label>
                    <input type="number" class="form-control" name="base_price" value="{{ old('base_price') }}" required>
                    @error('base_price')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Deposit (Rp)') }}</label>
                    <input type="number" class="form-control" name="deposit" value="{{ old('deposit') }}">
                    @error('deposit')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label class="form-label">{{ __('Fasilitas') }}</label>
                    <div class="form-text">{{ __('Centang fasilitas yang tersedia di tipe kamar ini.') }}</div>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2 mt-2">
                        @php
                            $facilityOptions = [
                                'AC', 'Wi-Fi', 'Air Panas', 'Lemari', 'Meja Belajar', 'Kamar Mandi Dalam',
                                'Televisi', 'Kasur King', 'Balkon', 'Dapur Bersama', 'Laundry', 'Parkir Motor',
                                'Parkir Mobil', 'Kulkas', 'Dispenser', 'Ruang Tamu', 'Cleaning Service', 'Keamanan 24 Jam'
                            ];
                        @endphp
                        @foreach ($facilityOptions as $facility)
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           id="facility_{{ Str::slug($facility) }}"
                                           name="facilities[]"
                                           value="{{ $facility }}"
                                           @checked(in_array($facility, old('facilities', []), true))>
                                    <label class="form-check-label" for="facility_{{ Str::slug($facility) }}">
                                        {{ $facility }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('facilities')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit">{{ __('Simpan') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('room-type-form');
                const propertySelect = form?.querySelector('select[name="property_id"]');

                if (form && propertySelect) {
                    const updateAction = () => {
                        const base = propertySelect.dataset.actionBase;
                        form.action = propertySelect.value ? `${base}/${propertySelect.value}/room-types` : '#';
                    };
                    propertySelect.addEventListener('change', updateAction);
                    updateAction();
                }
            });
</script>
@endpush
