<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('admin.moderations.index') }}" class="d-inline-flex align-items-center text-decoration-none text-muted mb-1">
                <span class="me-1">&larr;</span>{{ __('Back to moderation queue') }}
            </a>
            <h1 class="h4 text-dark mb-0">{{ $property->name }}</h1>
            <p class="text-muted small mb-0">{{ $property->address }}</p>
        </div>
        <div class="text-end">
            <span class="badge text-bg-warning text-uppercase">{{ __('Pending review') }}</span>
        </div>
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
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h2 class="h5 mb-0">{{ __('Owner Information') }}</h2>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted">{{ __('Owner') }}</dt>
                            <dd class="col-sm-8 fw-semibold">{{ $property->owner?->name ?? __('Unknown owner') }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Email') }}</dt>
                            <dd class="col-sm-8">{{ $property->owner?->email ?? '—' }}</dd>

                            <dt class="col-sm-4 text-muted">{{ __('Phone') }}</dt>
                            <dd class="col-sm-8">{{ $property->owner?->phone ?: __('Not provided') }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-start">
                        <div>
                            <h2 class="h5 mb-1">{{ __('Property Details') }}</h2>
                            <p class="text-muted small mb-0">
                                {{ __('Created at :date', ['date' => optional($property->created_at)->translatedFormat('d M Y H:i') ?? '—']) }}
                            </p>
                        </div>
                        <span class="badge text-bg-light text-dark">{{ __('Submission data') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h3 class="h6 text-uppercase text-muted mb-2">{{ __('Rules') }}</h3>
                            @if ($property->rules_text)
                                <p class="mb-0 text-secondary">{!! nl2br(e($property->rules_text)) !!}</p>
                            @else
                                <p class="mb-0 text-muted">{{ __('Owner has not provided specific rules yet.') }}</p>
                            @endif
                        </div>

                        <div class="row g-3 mb-4">
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

                        <div>
                            <h3 class="h6 text-uppercase text-muted mb-2">{{ __('Uploaded Photos') }}</h3>
                            @if (is_array($property->photos) && count($property->photos))
                                <div class="row g-2">
                                    @foreach ($property->photos as $photo)
                                        <div class="col-12 col-md-6">
                                            <div class="ratio ratio-4x3 border rounded overflow-hidden">
                                                <img src="{{ $photo }}" alt="{{ $property->name }}" class="w-100 h-100" style="object-fit: cover;">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="mb-0 text-muted">{{ __('No photos provided.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h2 class="h5 mb-0">{{ __('Room Types & Units') }}</h2>
                    </div>
                    <div class="card-body">
                        @forelse ($property->roomTypes as $roomType)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <h3 class="h6 mb-1">{{ $roomType->name }}</h3>
                                        <p class="small text-muted mb-0">
                                            {{ __('Base price: Rp:price / month', ['price' => number_format($roomType->base_price ?? 0, 0, ',', '.')]) }}
                                        </p>
                                    </div>
                                    <span class="badge rounded-pill text-bg-light text-dark">
                                        {{ __('Rooms: :count', ['count' => $roomType->rooms->count()]) }}
                                    </span>
                                </div>
                                <div class="row row-cols-1 row-cols-md-3 g-3 mt-2">
                                    @forelse ($roomType->rooms as $room)
                                        <div class="col">
                                            <div class="border rounded h-100 p-3">
                                                <p class="fw-semibold mb-1">{{ __('Room :code', ['code' => $room->room_code]) }}</p>
                                                <p class="small text-muted mb-0">
                                                    {{ __('Status: :status', ['status' => ucfirst($room->status)]) }}
                                                </p>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col">
                                            <div class="border rounded h-100 p-3 d-flex align-items-center justify-content-center text-center">
                                                <p class="small text-muted mb-0">{{ __('No rooms registered for this type.') }}</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <p class="mb-0 text-muted">{{ __('Owner has not added any room type yet.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4 d-flex flex-column gap-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h2 class="h6 text-uppercase text-muted mb-0">{{ __('Submission Summary') }}</h2>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted">{{ __('Status') }}</span>
                                <span class="badge text-bg-warning text-uppercase">{{ $property->status }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted">{{ __('Submitted') }}</span>
                                <span>{{ optional($property->created_at)->diffForHumans() ?? '—' }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted">{{ __('Existing notes') }}</span>
                                <span>{{ $property->moderation_notes ? __('Yes') : __('No') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h2 class="h5 mb-0">{{ __('Moderation Decision') }}</h2>
                        <p class="text-muted small mb-0">{{ __('Tambahkan catatan agar pemilik memahami keputusan Anda.') }}</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.moderations.approve', $property) }}" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <label for="approve_notes" class="form-label">{{ __('Optional notes for owner') }}</label>
                                <textarea
                                    id="approve_notes"
                                    name="moderation_notes"
                                    rows="3"
                                    class="form-control"
                                    placeholder="{{ __('Example: Sudah siap tayang, silakan pantau okupansi.') }}"
                                ></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                {{ __('Approve Property') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.moderations.reject', $property) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="reject_notes" class="form-label">{{ __('Reason for rejection') }}</label>
                                <textarea
                                    id="reject_notes"
                                    name="moderation_notes"
                                    rows="3"
                                    class="form-control @error('moderation_notes') is-invalid @enderror"
                                    placeholder="{{ __('Example: Foto kamar kurang jelas, mohon unggah ulang.') }}"
                                    required
                                >{{ old('moderation_notes') }}</textarea>
                                @error('moderation_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-outline-danger w-100">
                                {{ __('Reject Property') }}
                            </button>
                        </form>
                    </div>
                </div>

                @if ($property->moderation_notes)
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-0">
                            <h2 class="h6 text-uppercase text-muted mb-0">{{ __('Previous Notes') }}</h2>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 text-secondary">{!! nl2br(e($property->moderation_notes)) !!}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
