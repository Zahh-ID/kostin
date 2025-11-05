<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="grid gap-6 lg:grid-cols-[320px_1fr] h-[75vh]">
        <aside class="bg-white shadow-sm rounded-lg border border-gray-200 flex flex-col">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Percakapan</h2>
                <div class="mt-3">
                    <input
                        type="search"
                        wire:model.debounce.500ms="search"
                        placeholder="Cari percakapan..."
                        class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    />
                </div>
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-gray-100">
                @forelse ($conversations as $conversation)
                    <button
                        type="button"
                        wire:click="selectConversation({{ $conversation['id'] }})"
                        class="w-full text-left px-4 py-3 hover:bg-indigo-50 @if($activeConversationId === $conversation['id']) bg-indigo-50 border-l-4 border-indigo-500 @endif"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-900">{{ $conversation['title'] }}</span>
                            @if ($conversation['last_message_time'])
                                <span class="text-xs text-gray-500">{{ $conversation['last_message_time'] }}</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                            {{ $conversation['last_message_preview'] ?? 'Belum ada pesan' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-2">
                            {{ collect($conversation['participants'])->pluck('name')->implode(', ') }}
                        </p>
                    </button>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        Belum ada percakapan yang tersedia.
                    </div>
                @endforelse
            </div>
        </aside>

        <section class="bg-white shadow-sm rounded-lg border border-gray-200 flex flex-col">
            @if ($activeConversationId === null)
                <div class="flex-1 flex items-center justify-center text-gray-500">
                    Pilih percakapan untuk mulai mengirim pesan.
                </div>
            @else
                <div id="chatMessages" class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                    @forelse ($messages as $message)
                        <div class="flex {{ $message['is_mine'] ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-lg px-4 py-2 rounded-xl {{ $message['is_mine'] ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                                <p class="text-sm font-medium">
                                    {{ $message['user']['name'] ?? 'Pengguna' }}
                                </p>
                                <p class="mt-1 whitespace-pre-line break-words">
                                    {{ $message['body'] }}
                                </p>
                                <p class="mt-2 text-xs {{ $message['is_mine'] ? 'text-indigo-100' : 'text-gray-500' }}">
                                    {{ $message['created_at_for_humans'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500">Belum ada pesan.</div>
                    @endforelse
                </div>

                <form wire:submit.prevent="sendMessage" class="border-t border-gray-200 p-4">
                    <div class="flex items-end gap-3">
                        <textarea
                            wire:model.defer="messageBody"
                            rows="2"
                            placeholder="Tulis pesan..."
                            class="flex-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        ></textarea>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50"
                            @disabled($messageBody === '')
                        >
                            Kirim
                        </button>
                    </div>
                    @error('messageBody')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </form>
            @endif
        </section>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:load', () => {
            const subscriptions = {};

            Livewire.on('chat-subscribe', ({ conversationId }) => {
                const channelName = `conversations.${conversationId}`;
                if (subscriptions[channelName]) {
                    return;
                }

                subscriptions[channelName] = window.Echo.private(channelName)
                    .listen('.message.sent', (event) => {
                        Livewire.dispatch('chat-message-received', event);
                    });
            });

            Livewire.on('chat-scroll-to-bottom', () => {
                const container = document.getElementById('chatMessages');
                if (container) {
                    requestAnimationFrame(() => {
                        container.scrollTop = container.scrollHeight;
                    });
                }
            });
        });
    </script>
@endpush
