<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 text-dark mb-0">{{ __('Detail Pengajuan') }}</h1>
            <p class="text-muted small mb-0">{{ __('Lihat status dan catatan dari pemilik kos.') }}</p>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        @if (session('status'))
            <div class="alert alert-info">{{ session('status') }}</div>
        @endif

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h2 class="h5">{{ $application->property?->name ?? __('Properti') }}</h2>
                        <p class="text-muted mb-0">{{ $application->property?->address }}</p>
                        <div class="mt-3">
                            <span class="badge bg-{{ $application->status === 'approved' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($application->status) }}
                            </span>
                            @if ($application->approved_at)
                                <span class="text-muted small ms-2">{{ __('Disetujui :date', ['date' => $application->approved_at->format('d M Y')]) }}</span>
                            @elseif ($application->rejected_at)
                                <span class="text-muted small ms-2">{{ __('Ditolak :date', ['date' => $application->rejected_at->format('d M Y')]) }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">{{ __('Detail Pengajuan') }}</h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted">{{ __('Tipe Kamar') }}</dt>
                            <dd class="col-sm-8">{{ $application->roomType?->name ?? __('Belum ditentukan') }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Tanggal Masuk') }}</dt>
                            <dd class="col-sm-8">{{ optional($application->preferred_start_date)->format('d M Y') ?? __('Fleksibel') }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Durasi') }}</dt>
                            <dd class="col-sm-8">{{ $application->duration_months }} {{ __('bulan') }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Jumlah Penghuni') }}</dt>
                            <dd class="col-sm-8">{{ $application->occupants_count }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Budget / Bulan') }}</dt>
                            <dd class="col-sm-8">Rp{{ number_format($application->budget_per_month ?? 0, 0, ',', '.') }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Status Pekerjaan') }}</dt>
                            <dd class="col-sm-8">{{ ucfirst(str_replace('_', ' ', $application->employment_status ?? __('unknown'))) }}</dd>

                            @if ($application->company_name)
                                <dt class="col-sm-4 text-muted">{{ __('Perusahaan / Kampus') }}</dt>
                                <dd class="col-sm-8">{{ $application->company_name }}</dd>
                            @endif

                            @if ($application->job_title)
                                <dt class="col-sm-4 text-muted">{{ __('Posisi / Program Studi') }}</dt>
                                <dd class="col-sm-8">{{ $application->job_title }}</dd>
                            @endif

                            <dt class="col-sm-4 text-muted">{{ __('Pendapatan / Bulan') }}</dt>
                            <dd class="col-sm-8">Rp{{ number_format($application->monthly_income ?? 0, 0, ',', '.') }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Kontak Darurat') }}</dt>
                            <dd class="col-sm-8">
                                {{ $application->emergency_contact_name }}<br>
                                <span class="text-muted">{{ $application->emergency_contact_phone }}</span>
                            </dd>

                            <dt class="col-sm-4 text-muted">{{ __('Kendaraan') }}</dt>
                            <dd class="col-sm-8">
                                {{ $application->has_vehicle ? __('Membawa kendaraan') : __('Tidak membawa kendaraan') }}
                                @if ($application->vehicle_notes)
                                    <div class="small text-muted">{{ $application->vehicle_notes }}</div>
                                @endif
                            </dd>

                            <dt class="col-sm-4 text-muted">{{ __('Catatan Anda') }}</dt>
                            <dd class="col-sm-8">{!! nl2br(e($application->tenant_notes ?: __('—'))) !!}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Catatan Pemilik') }}</dt>
                            <dd class="col-sm-8">{!! nl2br(e($application->owner_notes ?: __('Belum ada catatan'))) !!}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">{{ __('Kontak & Darurat') }}</h5>
                        <p class="mb-1">
                            <span class="text-muted small d-block">{{ __('Telepon') }}</span>
                            {{ $application->contact_phone ?? $application->tenant?->phone ?? '—' }}
                        </p>
                        @if ($application->contact_email ?? false)
                            <p class="mb-1">
                                <span class="text-muted small d-block">{{ __('Email Alternatif') }}</span>
                                {{ $application->contact_email }}
                            </p>
                        @endif
                        <div class="mt-3">
                            <span class="text-muted small d-block">{{ __('Kontak Darurat') }}</span>
                            <div>{{ $application->emergency_contact_name }}</div>
                            <div class="text-muted">{{ $application->emergency_contact_phone }}</div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">{{ __('Langkah Selanjutnya') }}</h5>
                        @if ($application->status === 'pending')
                            <p class="text-muted small">
                                {{ __('Pengajuan Anda sedang ditinjau oleh pemilik. Kami akan memberitahu Anda ketika ada keputusan.') }}
                            </p>
                        @elseif ($application->status === 'approved')
                            <p class="text-success small">
                                {{ __('Selamat! Pemilik telah menyetujui pengajuan Anda. Silakan cek kontrak aktif pada menu Kontrak.') }}
                            </p>
                        @else
                            <p class="text-muted small">
                                {{ __('Pengajuan ini ditolak. Anda dapat menghubungi pemilik atau mengajukan ulang.') }}
                            </p>
                        @endif
                        <a href="{{ route('tenant.applications.index') }}" class="btn btn-outline-primary w-100 mt-3">
                            {{ __('Kembali ke daftar') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
