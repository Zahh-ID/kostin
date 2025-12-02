<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 text-dark mb-0">{{ __('Pengajuan Kontrak') }}</h1>
            <p class="text-muted small mb-0">{{ __('Pantau dan kelola seluruh pengajuan kontrak Anda.') }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form action="{{ route('tenant.applications.index') }}" method="get" class="d-flex gap-2">
                <input type="search" class="form-control form-control-sm" name="q" value="{{ request('q') }}" placeholder="{{ __('Cari properti untuk diajukan') }}">
                <button class="btn btn-outline-primary btn-sm" type="submit">{{ __('Cari') }}</button>
            </form>
            <a href="{{ route('tenant.applications.create') }}" class="btn btn-primary">
                {{ __('Ajukan Kontrak Baru') }}
            </a>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        @if (isset($searchProperties) && $searchProperties->isNotEmpty())
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">{{ __('Hasil Pencarian Properti') }}</h5>
                    <small class="text-muted">{{ __('Pilih properti untuk ajukan kontrak.') }}</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach ($searchProperties as $property)
                            <div class="col-md-6 col-xl-4">
                                <div class="border rounded-3 p-3 h-100 d-flex flex-column">
                                    <div class="fw-semibold mb-1">{{ $property->name }}</div>
                                    <div class="text-muted small mb-2">{{ $property->address }}</div>
                                    <p class="small text-muted flex-grow-1">{{ Str::limit($property->rules_text, 120) ?: __('Belum ada peraturan tertulis.') }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="badge bg-primary-subtle text-primary">{{ __('Approved') }}</span>
                                        <a href="{{ route('tenant.applications.create', ['property_id' => $property->id]) }}" class="btn btn-sm btn-primary">
                                            {{ __('Ajukan') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">{{ __('Pengajuan Saya') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>{{ __('Properti') }}</th>
                            <th>{{ __('Tipe Kamar') }}</th>
                            <th>{{ __('Tanggal Masuk') }}</th>
                            <th>{{ __('Durasi / Penghuni') }}</th>
                            <th>{{ __('Budget') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Aksi') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($applications as $application)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $application->property?->name ?? '—' }}</div>
                                    <div class="text-muted small">{{ $application->property?->address }}</div>
                                </td>
                                <td>{{ $application->roomType?->name ?? __('Belum ditentukan') }}</td>
                                <td>{{ optional($application->preferred_start_date)->format('d M Y') ?? '—' }}</td>
                                <td>
                                    {{ $application->duration_months }} {{ __('bulan') }}<br>
                                    <span class="text-muted small">{{ __('Penghuni: :count', ['count' => $application->occupants_count ?? 1]) }}</span>
                                </td>
                                <td>Rp{{ number_format($application->budget_per_month ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $application->status === 'approved' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('tenant.applications.show', $application) }}" class="btn btn-outline-primary btn-sm">
                                        {{ __('Detail') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">{{ __('Belum ada pengajuan kontrak.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($applications instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="card-footer bg-white border-0">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
