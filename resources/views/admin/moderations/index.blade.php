<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="badge text-bg-warning text-uppercase">{{ __('Moderation') }}</span>
            <h1 class="h4 text-dark mb-0">{{ __('Pending Property Moderation') }}</h1>
            <p class="text-muted small mb-0">{{ __('Review incoming submissions before they go live for tenants.') }}</p>
        </div>
        <div class="text-end">
            <span class="badge text-bg-primary rounded-pill">
                {{ trans_choice('{0} Tidak ada antrean|{1} :count antrian|[2,*] :count antrian', $properties->total(), ['count' => $properties->total()]) }}
            </span>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="row g-4">
            <div class="col-12">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0 pb-0">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <h2 class="h5 mb-1">{{ __('Moderation Queue') }}</h2>
                                <p class="text-muted small mb-0">
                                    {{ __('Periksa detail properti dan catatan pemilik sebelum menentukan status.') }}
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge text-bg-light text-dark">
                                    {{ __('Pending') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">{{ __('Property') }}</th>
                                    <th scope="col" class="w-25">{{ __('Owner') }}</th>
                                    <th scope="col">{{ __('Address') }}</th>
                                    <th scope="col" class="text-end">{{ __('Submitted') }}</th>
                                    <th scope="col" class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($properties as $property)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="flex-shrink-0">
                                                    <span class="badge rounded-pill text-bg-warning">{{ __('Draft → Pending') }}</span>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $property->name }}</div>
                                                    @if ($property->moderation_notes)
                                                        <div class="text-muted small">{{ \Illuminate\Support\Str::limit($property->moderation_notes, 80) }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $property->owner?->name }}</div>
                                            <div class="text-muted small">{{ $property->owner?->email }}</div>
                                            @if ($property->owner?->phone)
                                                <div class="text-muted small">{{ $property->owner?->phone }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-muted small">{{ $property->address }}</div>
                                        </td>
                                        <td class="text-end text-muted small">
                                            {{ optional($property->created_at)->diffForHumans() ?? '–' }}
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.moderations.show', $property) }}" class="btn btn-outline-primary btn-sm">
                                                {{ __('Review') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <div class="fw-semibold mb-1">{{ __('Tidak ada properti yang menunggu moderasi.') }}</div>
                                                <p class="small mb-0">{{ __('Pemilik akan muncul di sini ketika mengirimkan properti untuk ditinjau.') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($properties instanceof \Illuminate\Contracts\Pagination\Paginator)
                        <div class="card-footer bg-white border-0">
                            {{ $properties->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
