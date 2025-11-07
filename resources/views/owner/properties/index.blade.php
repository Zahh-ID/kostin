<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 text-dark mb-0">{{ __('My Properties') }}</h1>
            <p class="text-muted small mb-0">{{ __('Kelola cabang kos, status moderasi, dan tindakan cepat.') }}</p>
        </div>
        <div>
            <a href="{{ route('owner.properties.create') }}" class="btn btn-primary">
                {{ __('Add Property') }}
            </a>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="h5 mb-1">{{ __('Property Portfolio') }}</h2>
                        <p class="text-muted small mb-0">{{ __('Lihat ringkasan properti dan lanjutkan tindakan moderasi.') }}</p>
                    </div>
                    <span class="badge text-bg-light text-dark">
                        {{ trans_choice('{0} 0 properti|{1} :count properti|[2,*] :count properti', $properties->total(), ['count' => $properties->total()]) }}
                    </span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('Property') }}</th>
                            <th scope="col">{{ __('Address') }}</th>
                            <th scope="col" class="text-center">{{ __('Room Types') }}</th>
                            <th scope="col" class="text-center">{{ __('Rooms') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col" class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($properties as $property)
                            @php
                                $statusClasses = [
                                    'draft' => 'text-bg-secondary',
                                    'pending' => 'text-bg-warning',
                                    'approved' => 'text-bg-success',
                                    'rejected' => 'text-bg-danger',
                                ];
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $property->name }}</div>
                                    <div class="text-muted small">{{ __('Updated :date', ['date' => optional($property->updated_at)->diffForHumans() ?? '—']) }}</div>
                                </td>
                                <td class="text-muted small">
                                    {{ $property->address }}
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill text-bg-light text-dark">
                                        {{ $property->roomTypes->count() }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill text-bg-light text-dark">
                                        {{ $property->roomTypes->flatMap->rooms->count() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $statusClasses[$property->status] ?? 'text-bg-secondary' }} text-uppercase">
                                        {{ $property->status }}
                                    </span>
                                    @if ($property->status === 'rejected' && $property->moderation_notes)
                                        <p class="small text-danger mb-0 mt-1">
                                            {{ \Illuminate\Support\Str::limit($property->moderation_notes, 120) }}
                                        </p>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
                                        <a href="{{ route('owner.properties.show', $property) }}" class="btn btn-outline-secondary btn-sm">
                                            {{ __('Details') }}
                                        </a>
                                        <a href="{{ route('owner.properties.edit', $property) }}" class="btn btn-outline-primary btn-sm">
                                            {{ __('Edit') }}
                                        </a>
                                        @if (in_array($property->status, ['draft', 'rejected'], true))
                                            <form method="POST" action="{{ route('owner.properties.submit', $property) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    {{ __('Submit') }}
                                                </button>
                                            </form>
                                        @elseif ($property->status === 'pending')
                                            <form method="POST" action="{{ route('owner.properties.withdraw', $property) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    {{ __('Cancel') }}
                                                </button>
                                            </form>
                                        @elseif ($property->status === 'approved')
                                            <form method="POST" action="{{ route('owner.properties.withdraw', $property) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    {{ __('Unpublish') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <div class="fw-semibold mb-1">{{ __('Belum ada properti yang didaftarkan.') }}</div>
                                        <p class="small mb-0">{{ __('Tambah properti pertama Anda untuk mulai menerima calon tenant.') }}</p>
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
</x-app-layout>
