@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('owner.rooms.index', ['property_id' => $selectedProperty->id]) }}" class="text-decoration-none small text-muted">&larr; {{ __('Kembali') }}</a>
    <h1 class="h4 fw-semibold mt-2 mb-4">{{ __('Tambah Kamar Baru') }}</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="mb-4">
                <p class="text-muted small mb-1">{{ __('Properti') }}</p>
                <h2 class="h6 mb-0">{{ $selectedProperty->name }}</h2>
                <p class="text-muted small mb-0">{{ $selectedProperty->address }}</p>
            </div>

            <form method="POST"
                  action="{{ route('owner.room-types.rooms.store', $defaultRoomType) }}"
                  class="row g-4"
                  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="room_type_id" value="{{ $defaultRoomType->id }}">

                <div class="col-md-4">
                    <label class="form-label">{{ __('Kode Kamar') }}</label>
                    <input type="text"
                           class="form-control @error('room_code') is-invalid @enderror"
                           name="room_code"
                           value="{{ old('room_code') }}"
                           placeholder="{{ __('Contoh: A-01') }}"
                           required>
                    @error('room_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('Status Kamar') }}</label>
                    <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                        @foreach (['available' => __('Tersedia'), 'occupied' => __('Terisi'), 'maintenance' => __('Perbaikan')] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', 'available') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('Harga / Bulan (Rp)') }}</label>
                    <input type="number"
                           class="form-control @error('custom_price') is-invalid @enderror"
                           name="custom_price"
                           value="{{ old('custom_price') }}"
                           placeholder="{{ __('Wajib diisi') }}"
                           required>
                    <div class="form-text">{{ __('Gunakan angka 0 jika harga mengikuti kesepakatan lain.') }}</div>
                    @error('custom_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">{{ __('Deskripsi Kamar') }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              name="description"
                              rows="4"
                              placeholder="{{ __('Tuliskan detail ukuran kamar, fasilitas unik, arah matahari, dll.') }}"
                              required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">{{ __('Foto Kamar') }}</label>
                    <div class="form-text">{{ __('Unggah minimal 1 foto. Format JPG/PNG, maks 5MB per file.') }}</div>
                    <input type="file"
                           class="form-control @error('photos') is-invalid @enderror @error('photos.*') is-invalid @enderror"
                           name="photos[]"
                           accept="image/*"
                           multiple
                           required>
                    @error('photos')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('photos.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">{{ __('Fasilitas Tambahan') }}</label>
                    <div class="form-text">{{ __('Centang fasilitas ekstra atau tambahkan item baru sesuai kondisi kamar.') }}</div>
                    @php
                        $facilityOptions = [
                            'AC', 'Wi-Fi', 'Air Panas', 'Lemari', 'Meja Belajar', 'Kamar Mandi Dalam',
                            'Televisi', 'Kasur King', 'Balkon', 'Dapur Bersama', 'Laundry', 'Parkir Motor',
                            'Parkir Mobil', 'Kulkas', 'Dispenser', 'Ruang Tamu', 'Cleaning Service', 'Keamanan 24 Jam'
                        ];
                    @endphp
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2 mt-2">
                        @foreach ($facilityOptions as $facility)
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="facility_{{ \Illuminate\Support\Str::slug($facility) }}"
                                           name="facilities_override[]"
                                           value="{{ $facility }}"
                                           @checked(in_array($facility, old('facilities_override', []), true))>
                                    <label class="form-check-label" for="facility_{{ \Illuminate\Support\Str::slug($facility) }}">
                                        {{ $facility }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3" id="custom-facility-wrapper">
                        <label class="form-label">{{ __('Fasilitas Lainnya') }}</label>
                        <div class="d-flex flex-column gap-2" id="custom-facility-list">
                            @php
                                $customFacilities = collect(old('facilities_override', []))
                                    ->reject(fn ($facility) => in_array($facility, $facilityOptions, true))
                                    ->values();
                            @endphp
                            @forelse ($customFacilities as $facility)
                                <input type="text" name="facilities_override[]" class="form-control" value="{{ $facility }}">
                            @empty
                                <input type="text" name="facilities_override[]" class="form-control" placeholder="{{ __('Contoh: View taman') }}">
                            @endforelse
                        </div>
                        <button type="button" class="btn btn-link px-0 mt-2" id="add-facility-row">
                            + {{ __('Tambah Fasilitas Lain') }}
                        </button>
                    </div>
                    @error('facilities_override')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit">
                        {{ __('Simpan Kamar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addFacilityBtn = document.getElementById('add-facility-row');
        const customFacilityList = document.getElementById('custom-facility-list');

        if (addFacilityBtn && customFacilityList) {
            addFacilityBtn.addEventListener('click', () => {
                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'facilities_override[]';
                input.className = 'form-control';
                input.placeholder = '{{ __('Contoh: View kolam renang') }}';
                customFacilityList.appendChild(input);
            });
        }
    });
</script>
@endpush
