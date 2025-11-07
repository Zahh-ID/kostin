<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 text-dark mb-0">{{ __('Ajukan Kontrak Baru') }}</h1>
            <p class="text-muted small mb-0">{{ __('Pilih properti dan lengkapi detail kebutuhan sewa Anda.') }}</p>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="POST" action="{{ route('tenant.applications.store') }}" class="row g-4">
                    @csrf
                    @if ($selectedPropertyModel)
                        <input type="hidden" name="property_id" value="{{ $selectedPropertyModel->id }}">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <h5 class="fw-semibold mb-1">{{ $selectedPropertyModel->name }}</h5>
                                    <p class="text-muted">{{ $selectedPropertyModel->address }}</p>
                                    <p class="small text-muted mb-0">{{ __('Pilih tipe kamar untuk melihat rincian harga dan fasilitas.') }}</p>
                                    <a href="{{ route('tenant.applications.create') }}" class="btn btn-link btn-sm px-0 mt-2">{{ __('Pilih properti lain') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="room_type_id" class="form-label">{{ __('Tipe Kamar') }}</label>
                            <select id="room_type_id" name="room_type_id" class="form-select" required>
                                <option value="">{{ __('Pilih tipe kamar yang tersedia') }}</option>
                                @foreach ($selectedPropertyModel->roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" @selected(old('room_type_id') == $roomType->id)>
                                        {{ $roomType->name }} — Rp{{ number_format($roomType->base_price ?? 0, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('room_type_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <h6 class="fw-semibold mb-3">{{ __('Rincian Kamar & Fasilitas') }}</h6>
                                    @foreach ($selectedPropertyModel->roomTypes as $roomType)
                                        <div class="border rounded-3 p-3 mb-3">
                                            <div class="d-flex justify-content-between flex-wrap gap-2">
                                                <div>
                                                    <p class="fw-semibold mb-1">{{ $roomType->name }}</p>
                                                    <small class="text-muted">
                                                        {{ __('Luas :area m² · Kamar mandi :type', ['area' => $roomType->area_m2 ?? '–', 'type' => $roomType->bathroom_type ?? '–']) }}
                                                    </small>
                                                </div>
                                                <span class="badge bg-primary-subtle text-primary">{{ __('Mulai Rp') }}{{ number_format($roomType->base_price ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            @if (! empty($roomType->facilities_json))
                                                <p class="text-muted small mb-0 mt-2">{{ __('Fasilitas:') }} {{ collect($roomType->facilities_json)->implode(', ') }}</p>
                                            @endif
                                            @if ($roomType->rooms->isNotEmpty())
                                                <div class="row g-3 mt-2">
                                                    @foreach ($roomType->rooms as $room)
                                                        @php
                                                            $photoIndex = ($loop->index + $roomType->id) % max(count($selectedPropertyModel->photos ?? []), 1);
                                                            $roomPhoto = $selectedPropertyModel->photos[$photoIndex] ?? 'https://picsum.photos/seed/'.$room->id.'/400/300';
                                                        @endphp
                                                        <div class="col-md-6 col-xl-4">
                                                            <div class="rounded-3 border p-2 h-100">
                                                                <div class="ratio ratio-4x3 rounded-3 overflow-hidden mb-2">
                                                                    <img src="{{ $roomPhoto }}" alt="{{ __('Kamar :code', ['code' => $room->room_code]) }}" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                                                </div>
                                                                <p class="fw-semibold mb-1">{{ __('Kamar :code', ['code' => $room->room_code]) }}</p>
                                                                <p class="text-muted small mb-1">{{ __('Status:') }} {{ ucfirst($room->status) }}</p>
                                                                <p class="text-muted small mb-0">
                                                                    {{ $room->custom_price ? __('Harga khusus: Rp').number_format($room->custom_price, 0, ',', '.') : __('Mengikuti harga dasar') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted small mt-2 mb-0">{{ __('Belum ada kamar aktif untuk tipe ini.') }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-12">
                            <div class="alert alert-info">
                                {{ __('Pilih salah satu properti di bawah untuk mulai mengisi detail pengajuan.') }}
                            </div>
                        </div>
                        @foreach ($properties as $property)
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="fw-semibold mb-1">{{ $property->name }}</h5>
                                        <p class="text-muted small mb-2">{{ $property->address }}</p>
                                        <p class="small text-muted">{{ Str::limit($property->rules_text, 120) ?: __('Belum ada peraturan tertulis.') }}</p>
                                    </div>
                                    <div class="card-footer bg-white border-0 text-end">
                                        <a href="{{ route('tenant.applications.create', ['property_id' => $property->id]) }}" class="btn btn-outline-primary btn-sm">
                                            {{ __('Pilih Properti Ini') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if ($selectedPropertyModel)
                        <div class="col-md-6">
                            <label for="preferred_start_date" class="form-label">{{ __('Tanggal Masuk yang Diinginkan') }}</label>
                            <input type="date" id="preferred_start_date" name="preferred_start_date" class="form-control" value="{{ old('preferred_start_date') }}">
                            @error('preferred_start_date')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="duration_months" class="form-label">{{ __('Durasi (bulan)') }}</label>
                            <input type="number" id="duration_months" name="duration_months" class="form-control" min="1" max="36" value="{{ old('duration_months', 12) }}" required>
                            @error('duration_months')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="fw-semibold mb-2">{{ __('Syarat & Ketentuan Properti') }}</h6>
                                    <div class="bg-light rounded p-3 mb-3">
                                        {!! $selectedPropertyModel->rules_text ? nl2br(e($selectedPropertyModel->rules_text)) : __('Pemilik belum menetapkan ketentuan khusus.') !!}
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="terms_agreed" name="terms_agreed" required>
                                        <label class="form-check-label" for="terms_agreed">
                                            {{ __('Saya telah membaca dan menyetujui syarat & ketentuan di atas.') }}
                                        </label>
                                    </div>
                                    @error('terms_agreed')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Nomor WhatsApp / Telepon') }}</label>
                            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" name="contact_phone" value="{{ old('contact_phone') }}" required>
                            @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Email Alternatif (opsional)') }}</label>
                            <input type="email" class="form-control @error('contact_email') is-invalid @enderror" name="contact_email" value="{{ old('contact_email') }}">
                            @error('contact_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('Rencana Tanggal Masuk') }}</label>
                            <input type="date" class="form-control @error('preferred_start_date') is-invalid @enderror" name="preferred_start_date" value="{{ old('preferred_start_date') }}">
                            @error('preferred_start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Durasi Sewa (bulan)') }}</label>
                            <input type="number" class="form-control @error('duration_months') is-invalid @enderror" name="duration_months" value="{{ old('duration_months', 12) }}" min="1" max="36" required>
                            @error('duration_months')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Jumlah Penghuni') }}</label>
                            <input type="number" class="form-control @error('occupants_count') is-invalid @enderror" name="occupants_count" value="{{ old('occupants_count', 1) }}" min="1" max="6" required>
                            @error('occupants_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Budget Sewa / Bulan (Rp)') }}</label>
                            <input type="number" class="form-control @error('budget_per_month') is-invalid @enderror" name="budget_per_month" value="{{ old('budget_per_month', $selectedPropertyModel->roomTypes->min('base_price')) }}" min="0" required>
                            @error('budget_per_month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Perkiraan Pendapatan / Bulan (Rp)') }}</label>
                            <input type="number" class="form-control @error('monthly_income') is-invalid @enderror" name="monthly_income" value="{{ old('monthly_income') }}" min="0" required>
                            @error('monthly_income')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Status Pekerjaan') }}</label>
                            <select class="form-select @error('employment_status') is-invalid @enderror" name="employment_status" required>
                                @foreach (['full_time' => __('Karyawan Tetap'), 'part_time' => __('Paruh Waktu'), 'student' => __('Mahasiswa'), 'freelance' => __('Freelance / Wirausaha'), 'other' => __('Lainnya')] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('employment_status', 'full_time') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('employment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Perusahaan / Kampus (opsional)') }}</label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" placeholder="{{ __('Nama perusahaan/kampus') }}">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Posisi / Program Studi (opsional)') }}</label>
                            <input type="text" class="form-control @error('job_title') is-invalid @enderror" name="job_title" value="{{ old('job_title') }}">
                            @error('job_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Kontak Darurat') }}</label>
                            <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="{{ __('Nama orang tua / wali') }}" required>
                            @error('emergency_contact_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('No. Kontak Darurat') }}</label>
                            <input type="text" class="form-control @error('emergency_contact_phone') is-invalid @enderror" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" required>
                            @error('emergency_contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Kendaraan') }}</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="has_vehicle" name="has_vehicle" value="1" @checked(old('has_vehicle'))>
                                    <label class="form-check-label" for="has_vehicle">{{ __('Saya membawa kendaraan pribadi') }}</label>
                                </div>
                                <input type="text" class="form-control @error('vehicle_notes') is-invalid @enderror" name="vehicle_notes" value="{{ old('vehicle_notes') }}" placeholder="{{ __('Contoh: Motor matic, 1 unit') }}">
                            </div>
                            @error('vehicle_notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="tenant_notes" class="form-label">{{ __('Catatan tambahan untuk pemilik') }}</label>
                            <textarea id="tenant_notes" name="tenant_notes" rows="4" class="form-control" placeholder="{{ __('Ceritakan kebutuhan khusus, jadwal survei, atau preferensi lainnya.') }}">{{ old('tenant_notes') }}</textarea>
                            @error('tenant_notes')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('tenant.applications.index') }}" class="btn btn-outline-secondary">{{ __('Batal') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Kirim Pengajuan') }}</button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
