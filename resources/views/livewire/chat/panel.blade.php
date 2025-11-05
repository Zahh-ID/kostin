<div class="row g-3" wire:poll.keep-alive.5s="refreshConversations">
    <div class="col-12 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h2 class="h6 mb-0">{{ __('Percakapan') }}</h2>
            </div>
            <div class="list-group list-group-flush rounded-bottom overflow-auto" style="max-height: 520px;">
                @forelse ($this->conversations as $conversation)
                    @php
                        $otherParticipants = $conversation->participants
                            ->where('id', '!=', auth()->id())
                            ->pluck('name')
                            ->implode(', ');
                        $title = $conversation->is_group
                            ? ($conversation->title ?? __('Grup'))
                            : ($otherParticipants ?: __('Percakapan'));
                        $latestMessage = $conversation->messages->first();
                        $lastReadAt = optional($conversation->pivot)->last_read_at
                            ? \Illuminate\Support\Carbon::parse($conversation->pivot->last_read_at)
                            : null;
                        $hasUnread = $latestMessage !== null && ($lastReadAt === null || $latestMessage->created_at->greaterThan($lastReadAt));
                    @endphp
                    <button
                        type="button"
                        wire:click="selectConversation({{ $conversation->id }})"
                        class="list-group-item list-group-item-action text-start @if ($conversation->id === $activeConversationId) active @endif"
                        wire:key="conversation-{{ $conversation->id }}"
                    >
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">{{ $title }}</span>
                            @if ($hasUnread)
                                <span class="badge bg-primary rounded-pill">{{ __('Baru') }}</span>
                            @endif
                        </div>
                        <small class="text-muted d-block mt-1">
                            @if ($latestMessage)
                                {{ $latestMessage->user?->name ? $latestMessage->user->name.': ' : '' }}{{ \Illuminate\Support\Str::limit($latestMessage->body, 60) }}
                            @else
                                {{ __('Belum ada pesan.') }}
                            @endif
                        </small>
                    </button>
                @empty
                    <div class="p-4 text-center text-muted">
                        <p class="mb-1">{{ __('Belum ada percakapan.') }}</p>
                        <p class="small mb-0">{{ __('Mulai chat dari halaman kontrak atau hubungi admin untuk bantuan.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8 col-xl-9">
        @if ($this->activeConversation)
            @php
                $conversation = $this->activeConversation;
                $headerTitle = $conversation->is_group
                    ? ($conversation->title ?? __('Grup'))
                    : $conversation->participants->where('id', '!=', auth()->id())->pluck('name')->implode(', ');
                $headerTitle = $headerTitle ?: __('Percakapan');
            @endphp
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h2 class="h6 mb-1">{{ $headerTitle }}</h2>
                            <small class="text-muted">
                                {{ __(':count anggota', ['count' => $conversation->participants->count()]) }}
                            </small>
                        </div>
                        <span class="badge bg-light text-dark">{{ __('Aktif') }}</span>
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="flex-grow-1 overflow-auto pe-2" style="max-height: 420px;">
                        @forelse ($conversation->messages as $message)
                            @php
                                $isMine = $message->user_id === auth()->id();
                            @endphp
                            <div class="d-flex mb-3 {{ $isMine ? 'justify-content-end' : 'justify-content-start' }}" wire:key="message-{{ $message->id }}">
                                <div class="d-inline-block px-3 py-2 rounded-3 {{ $isMine ? 'bg-primary text-white' : 'bg-light' }}">
                                    @unless ($isMine)
                                        <div class="fw-semibold small mb-1">{{ $message->user?->name ?? __('Pengguna') }}</div>
                                    @endunless
                                    <div>{{ $message->body }}</div>
                                    <div class="text-end small mt-1 {{ $isMine ? 'text-white-50' : 'text-muted' }}">
                                        {{ optional($message->created_at)->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">{{ __('Belum ada pesan di percakapan ini.') }}</p>
                        @endforelse
                    </div>
                    <form wire:submit.prevent="sendMessage" class="mt-3">
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control @error('message') is-invalid @enderror"
                                placeholder="{{ __('Tulis pesan...') }}"
                                wire:model.defer="message"
                            >
                            <button class="btn btn-primary" type="submit">
                                {{ __('Kirim') }}
                            </button>
                            @error('message')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm h-100 d-flex align-items-center justify-content-center">
                <div class="text-center text-muted">
                    <div class="display-6 mb-3" aria-hidden="true">💬</div>
                    <h2 class="h5">{{ __('Pilih percakapan') }}</h2>
                    <p class="mb-0">{{ __('Mulai chat untuk berkomunikasi dengan pemilik atau admin.') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
