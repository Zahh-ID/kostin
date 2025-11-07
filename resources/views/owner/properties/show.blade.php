<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('owner.properties.index') }}" class="d-inline-flex align-items-center text-decoration-none text-muted mb-1">
                <span class="me-1">&larr;</span>{{ __('Back to property list') }}
            </a>
            <h1 class="h4 text-dark mb-0">{{ $property->name }}</h1>
            <p class="text-muted small mb-0">{{ $property->address }}</p>
        </div>
        @php
            $statusClasses = [
                'draft' => 'text-bg-secondary',
                'pending' => 'text-bg-warning',
                'approved' => 'text-bg-success',
                'rejected' => 'text-bg-danger',
            ];
        @endphp
        <span class="badge {{ $statusClasses[$property->status] ?? 'text-bg-secondary' }} text-uppercase">
            {{ $property->status }}
        </span>
    </x-slot>

    <div class="container-fluid py-4">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-12 col-xl-8 d-flex flex-column gap-4">
                @if ($property->status === 'pending')
                    <div class="alert alert-warning border-0 shadow-sm">
                        <div class="fw-semibold mb-1">{{ __('Menunggu persetujuan admin') }}</div>
                        <p class="small mb-0">
                            {{ __('Perubahan sementara dikunci sampai tim admin menyelesaikan moderasi.') }}
                        </p>
                    </div>
                @elseif ($property->status === 'rejected')
                    <div class="alert alert-danger border-0 shadow-sm">
                        <div class="fw-semibold mb-1">{{ __('Moderasi ditolak') }}</div>
                        <p class="small mb-0">
                            {{ $property->moderation_notes ?: __('Admin belum menyertakan catatan detail. Silakan perbarui informasi dan ajukan kembali.') }}
                        </p>
                    </div>
                @elseif ($property->status === 'approved')
                    <div class="alert alert-success border-0 shadow-sm">
                        <div class="fw-semibold mb-1">{{ __('Properti telah tayang') }}</div>
                        <p class="small mb-0">
                            {{ __('Disetujui oleh :moderator pada :date.', [
                                'moderator' => $property->moderator?->name ?? __('admin'),
                                'date' => optional($property->moderated_at)->translatedFormat('d M Y H:i') ?? __('-'),
                            ]) }}
                        </p>
                        @if ($property->moderation_notes)
                            <p class="small mb-0 mt-2">{{ $property->moderation_notes }}</p>
                        @endif
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">{{ __('Room Types & Units') }}</h2>
                        <a href="{{ route('owner.room-types.index') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('Manage Types') }}
                        </a>
                    </div>
                    <div class="card-body">
                        @forelse ($property->roomTypes as $roomType)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <h3 class="h6 mb-1">{{ $roomType->name }}</h3>
                                        <p class="small text-muted mb-0">
                                            {{ __('Area') }} {{ $roomType->area_m2 ?? '–' }} m² · {{ __('Bathroom') }} {{ $roomType->bathroom_type ?? '–' }}
                                        </p>
                                    </div>
                                    <span class="badge text-bg-light text-dark">
                                        Rp{{ number_format($roomType->base_price ?? 0, 0, ',', '.') }}/{{ __('month') }}
                                    </span>
                                </div>
                                <div class="row row-cols-1 row-cols-md-3 g-3 mt-3">
                                    @forelse ($roomType->rooms as $room)
                                        <div class="col">
                                            <div class="border rounded h-100 p-3">
                                                <p class="fw-semibold mb-1">{{ __('Room') }} {{ $room->room_code }}</p>
                                                <p class="small text-muted mb-1">{{ __('Status') }}: {{ ucfirst($room->status) }}</p>
                                                <p class="small text-muted mb-0">
                                                    {{ __('Last contract') }}:
                                                    {{ optional($room->contracts->first())->start_date?->format('d M Y') ?? '–' }}
                                                </p>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col">
                                            <div class="border rounded h-100 p-3 d-flex align-items-center justify-content-center text-center">
                                                <p class="small text-muted mb-0">{{ __('No rooms for this type.') }}</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('No room types yet. Add them through the room types menu.') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h2 class="h5 mb-0">{{ __('Rules & Location') }}</h2>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h3 class="h6 text-uppercase text-muted mb-2">{{ __('Rules') }}</h3>
                            <p class="mb-0 text-secondary">
                                {!! $property->rules_text ? nl2br(e($property->rules_text)) : __('No specific rules yet.') !!}
                            </p>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <span class="text-muted text-uppercase small d-block">{{ __('Latitude') }}</span>
                                    <span class="fw-semibold">{{ $property->lat ?? __('Not set') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <span class="text-muted text-uppercase small d-block">{{ __('Longitude') }}</span>
                                    <span class="fw-semibold">{{ $property->lng ?? __('Not set') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4 d-flex flex-column gap-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h2 class="h6 mb-0 text-uppercase text-muted">{{ __('Recent Tasks') }}</h2>
                        <a href="{{ route('owner.shared-tasks.index') }}" class="btn btn-sm btn-outline-secondary">
                            {{ __('View All') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse ($property->sharedTasks as $task)
                                <li class="list-group-item px-0">
                                    <div class="fw-semibold">{{ $task->title }}</div>
                                    <div class="text-muted small">
                                        {{ optional($task->next_run_at)->format('d M Y') ?? __('Flexible schedule') }}
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted">{{ __('No tasks for this property.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h2 class="h5 mb-0">{{ __('Quick Actions') }}</h2>
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <a href="{{ route('owner.properties.edit', $property) }}" class="btn btn-primary">
                            {{ __('Edit Property') }}
                        </a>

                        @if (in_array($property->status, ['draft', 'rejected'], true))
                            <form method="POST" action="{{ route('owner.properties.submit', $property) }}">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    {{ __('Submit for Moderation') }}
                                </button>
                            </form>
                        @elseif ($property->status === 'pending')
                            <form method="POST" action="{{ route('owner.properties.withdraw', $property) }}">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100">
                                    {{ __('Cancel Submission') }}
                                </button>
                            </form>
                        @elseif ($property->status === 'approved')
                            <form method="POST" action="{{ route('owner.properties.withdraw', $property) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    {{ __('Unpublish Property') }}
                                </button>
                            </form>
                            <a href="{{ route('property.show', $property) }}" target="_blank" class="btn btn-outline-success">
                                {{ __('View Public Page') }}
                            </a>
                        @endif

                        <a href="{{ route('owner.rooms.create', ['property_id' => $property->id]) }}" class="btn btn-outline-secondary">
                            {{ __('Add Room') }}
                        </a>
                        <a href="{{ route('owner.rooms.index', ['property_id' => $property->id]) }}" class="btn btn-outline-secondary">
                            {{ __('Manage Rooms') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
