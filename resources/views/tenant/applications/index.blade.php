<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 text-dark mb-0">{{ __('Pengajuan Kontrak') }}</h1>
            <p class="text-muted small mb-0">{{ __('Pantau dan kelola seluruh pengajuan kontrak Anda.') }}</p>
        </div>
        <a href="{{ route('tenant.applications.create') }}" class="btn btn-primary">
            {{ __('Ajukan Kontrak Baru') }}
        </a>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm">
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
