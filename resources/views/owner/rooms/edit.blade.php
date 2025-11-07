@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('owner.rooms.index', ['property_id' => optional($contextProperty)->id]) }}" class="text-decoration-none small text-muted">&larr; {{ __('Kembali') }}</a>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-2 mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Perbarui Kamar') }} {{ $room->room_code }}</h1>
            <p class="text-muted mb-0">
                {{ $room->roomType?->property?->name }} &middot; {{ $room->roomType?->name }}
            </p>
        </div>
        <span class="badge bg-secondary-subtle text-secondary-emphasis align-self-start">
            {{ __('Dibuat:') }} {{ optional($room->created_at)->format('d M Y') }}
        </span>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="POST"
                          action="{{ route('owner.rooms.update', ['room' => $room, 'property_id' => optional($contextProperty)->id]) }}"
                          class="row g-4"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">{{ __('Tipe Kamar') }}</label>
                        <div class="fw-semibold mb-1">{{ $room->roomType?->name }}</div>
                        <div class="text-muted small">{{ $contextProperty?->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">{{ __('Kode Kamar') }}</label>
                            <div class="fw-semibold">{{ $room->room_code }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Status Kamar') }}</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                @foreach (['available' => __('Tersedia'), 'occupied' => __('Terisi'), 'maintenance' => __('Perbaikan')] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $room->status) === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Harga / Bulan (Rp)') }}</label>
                            <input type="number"
                                   class="form-control @error('custom_price') is-invalid @enderror"
                                   name="custom_price"
                                   value="{{ old('custom_price', $room->custom_price) }}"
                                   required>
                            <div class="form-text">{{ __('Gunakan angka 0 jika mengikuti kesepakatan lain.') }}</div>
                            @error('custom_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('Deskripsi Kamar') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  name="description"
                                  rows="4"
                                  required>{{ old('description', $room->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('Foto Kamar') }}</label>
                        <div class="form-text">{{ __('Anda dapat menghapus foto lama atau menambahkan foto baru (maks 5MB per file).') }}</div>
                        <div class="row g-3 mt-1">
                            @forelse ($room->photos_json ?? [] as $photo)
                                <div class="col-6 col-md-3">
                                    <div class="border rounded-3 overflow-hidden position-relative">
                                        <img src="{{ asset('storage/'.$photo) }}" alt="Room photo" class="img-fluid">
                                        <div class="form-check position-absolute top-0 end-0 m-2 bg-white rounded-pill px-2">
                                            <input class="form-check-input" type="checkbox" name="remove_photos[]" value="{{ $photo }}" id="remove_{{ \Illuminate\Support\Str::slug($photo) }}">
                                            <label class="form-check-label small" for="remove_{{ \Illuminate\Support\Str::slug($photo) }}">{{ __('Hapus') }}</label>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">{{ __('Belum ada foto tersimpan.') }}</p>
                            @endforelse
                        </div>
                        <input type="file"
                               class="form-control mt-3 @error('photos') is-invalid @enderror @error('photos.*') is-invalid @enderror"
                               name="photos[]"
                               accept="image/*"
                               multiple>
                        @error('photos')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('photos.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Fasilitas Tambahan') }}</label>
                            <div class="form-text">{{ __('Centang fasilitas yang hanya tersedia pada kamar ini.') }}</div>
                            @php
                                $facilityOptions = [
                                    'AC', 'Wi-Fi', 'Air Panas', 'Lemari', 'Meja Belajar', 'Kamar Mandi Dalam',
                                    'Televisi', 'Kasur King', 'Balkon', 'Dapur Bersama', 'Laundry', 'Parkir Motor',
                                    'Parkir Mobil', 'Kulkas', 'Dispenser', 'Ruang Tamu', 'Cleaning Service', 'Keamanan 24 Jam'
                                ];
                                $currentFacilities = collect(old('facilities_override', $room->facilities_override_json ?? []));
                                $customFacilities = $currentFacilities
                                    ->reject(fn ($facility) => in_array($facility, $facilityOptions, true))
                                    ->values();
                            @endphp
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2 mt-2">
                                @foreach ($facilityOptions as $facility)
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   id="facility_edit_{{ \Illuminate\Support\Str::slug($facility) }}"
                                                   name="facilities_override[]"
                                                   value="{{ $facility }}"
                                                   @checked($currentFacilities->contains($facility))>
                                            <label class="form-check-label" for="facility_edit_{{ \Illuminate\Support\Str::slug($facility) }}">
                                                {{ $facility }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3" id="custom-facility-wrapper">
                                <label class="form-label">{{ __('Fasilitas Lainnya') }}</label>
                                <div class="d-flex flex-column gap-2" id="custom-facility-list">
                                    @forelse ($customFacilities as $facility)
                                        <input type="text" name="facilities_override[]" class="form-control" value="{{ $facility }}">
                                    @empty
                                        <input type="text" name="facilities_override[]" class="form-control" placeholder="{{ __('Contoh: View kota') }}">
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

                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('owner.rooms.show', ['room' => $room, 'property_id' => optional($contextProperty)->id]) }}" class="btn btn-outline-secondary">
                                {{ __('Batalkan') }}
                            </a>
                            <button class="btn btn-primary" type="submit">
                                {{ __('Simpan Perubahan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted">{{ __('Ringkasan Kontrak Terakhir') }}</h2>
                    <hr>
                    @forelse ($room->contracts as $contract)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fw-semibold">{{ $contract->tenant?->name ?? __('Tanpa Penyewa') }}</div>
                            <div class="text-muted small">
                                {{ optional($contract->start_date)->format('d M Y') }} -
                                {{ optional($contract->end_date)->format('d M Y') ?? __('Berjalan') }}
                            </div>
                            <span class="badge bg-light text-dark mt-2">{{ ucfirst($contract->status) }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('Belum ada riwayat kontrak untuk kamar ini.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addButton = document.getElementById('add-facility-row');
        const container = document.getElementById('custom-facility-list');

        if (addButton && container) {
            addButton.addEventListener('click', () => {
                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'facilities_override[]';
                input.className = 'form-control';
                input.placeholder = '{{ __('Contoh: Balkon privat') }}';
                container.appendChild(input);
            });
        }
    });
</script>
@endpush
