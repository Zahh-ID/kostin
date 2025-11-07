<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('owner.applications.index') }}" class="text-decoration-none small text-muted">&larr; {{ __('Kembali') }}</a>
            <h1 class="h4 text-dark mb-0 mt-1">{{ __('Pengajuan oleh :tenant', ['tenant' => $application->tenant?->name ?? __('Tenant')]) }}</h1>
        </div>
        <span class="badge bg-{{ $application->status === 'approved' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }}">
            {{ ucfirst($application->status) }}
        </span>
    </x-slot>

    <div class="container-fluid py-4">
        @if (session('status'))
            <div class="alert alert-info">{{ session('status') }}</div>
        @endif

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">{{ __('Informasi Tenant') }}</h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted">{{ __('Nama') }}</dt>
                            <dd class="col-sm-8">{{ $application->tenant?->name ?? '—' }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Email') }}</dt>
                            <dd class="col-sm-8">{{ $application->tenant?->email ?? '—' }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Telepon') }}</dt>
                            <dd class="col-sm-8">{{ $application->tenant?->phone ?? '—' }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">{{ __('Detail Pengajuan') }}</h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted">{{ __('Properti') }}</dt>
                            <dd class="col-sm-8">{{ $application->property?->name }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Tipe Kamar') }}</dt>
                            <dd class="col-sm-8">{{ $application->roomType?->name ?? __('—') }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Tanggal Masuk') }}</dt>
                            <dd class="col-sm-8">{{ optional($application->preferred_start_date)->format('d M Y') ?? __('Fleksibel') }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Durasi') }}</dt>
                            <dd class="col-sm-8">{{ $application->duration_months }} {{ __('bulan') }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Catatan Tenant') }}</dt>
                            <dd class="col-sm-8">{{ $application->tenant_notes ?: __('Tidak ada catatan tambahan.') }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Catatan Anda') }}</dt>
                            <dd class="col-sm-8">{{ $application->owner_notes ?: __('Belum ada catatan.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                @if ($application->status === 'pending')
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-0">
                            <h5 class="fw-semibold mb-0">{{ __('Setujui Pengajuan') }}</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('owner.applications.update', $application) }}" class="row g-3">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="approve">

                                <div class="col-12">
                                    <label for="room_id" class="form-label">{{ __('Pilih Kamar') }}</label>
                                    <select id="room_id" name="room_id" class="form-select" required>
                                        <option value="">{{ __('Pilih kamar tersedia') }}</option>
                                        @foreach ($application->property->roomTypes as $roomType)
                                            @foreach ($roomType->rooms as $room)
                                                <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>
                                                    {{ $roomType->name }} — {{ __('Kamar :code', ['code' => $room->room_code]) }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    @error('room_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">{{ __('Tanggal Mulai') }}</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', optional($application->preferred_start_date)->format('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="duration_months" class="form-label">{{ __('Durasi (bulan)') }}</label>
                                    <input type="number" id="duration_months" name="duration_months" class="form-control" value="{{ old('duration_months', $application->duration_months) }}" min="1" max="36" required>
                                    @error('duration_months')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="price_per_month" class="form-label">{{ __('Harga per Bulan (Rp)') }}</label>
                                    <input type="number" id="price_per_month" name="price_per_month" class="form-control" value="{{ old('price_per_month', optional($application->roomType)->base_price) }}" min="0" required>
                                    @error('price_per_month')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="billing_day" class="form-label">{{ __('Tanggal Penagihan') }}</label>
                                    <input type="number" id="billing_day" name="billing_day" class="form-control" value="{{ old('billing_day', 5) }}" min="1" max="28" required>
                                    @error('billing_day')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="owner_notes" class="form-label">{{ __('Catatan untuk Tenant (opsional)') }}</label>
                                    <textarea id="owner_notes" name="owner_notes" rows="3" class="form-control">{{ old('owner_notes') }}</textarea>
                                    @error('owner_notes')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success">{{ __('Setujui & Buat Kontrak') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0">
                            <h5 class="fw-semibold mb-0 text-danger">{{ __('Tolak Pengajuan') }}</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('owner.applications.update', $application) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="reject">
                                <div class="mb-3">
                                    <label for="reject_notes" class="form-label">{{ __('Alasan Penolakan') }}</label>
                                    <textarea id="reject_notes" name="owner_notes" rows="3" class="form-control" required>{{ old('owner_notes') }}</textarea>
                                    @error('owner_notes')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-outline-danger w-100">{{ __('Tolak Pengajuan') }}</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-3">{{ __('Status Pengajuan') }}</h5>
                            @if ($application->status === 'approved')
                                <p class="text-success mb-0">{{ __('Pengajuan ini telah disetujui. Kontrak terkait ada pada menu Kontrak.') }}</p>
                            @else
                                <p class="text-muted mb-0">{{ __('Pengajuan ini telah ditolak.') }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
