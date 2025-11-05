<?php

namespace App\Livewire\Chat;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatBoard extends Component
{
    public array $conversations = [];

    public ?int $activeConversationId = null;

    public array $messages = [];

    public string $messageBody = '';

    public string $search = '';

    public function mount(): void
    {
        $this->loadConversations();

        if ($this->activeConversationId === null && ! empty($this->conversations)) {
            $this->selectConversation($this->conversations[0]['id']);
        }
    }

    public function updatingSearch(): void
    {
        $this->loadConversations();
    }

    public function loadConversations(): void
    {
        $user = Auth::user();

        $conversations = $user->conversations()
            ->with(['participants:id,name'])
            ->withCount('messages')
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($inner): void {
                    $inner->where('title', 'like', '%'.$this->search.'%')
                        ->orWhereHas('participants', function ($participant): void {
                            $participant->where('name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('messages_count')
            ->get();

        $this->conversations = $conversations->map(function (Conversation $conversation) use ($user): array {
            $title = $conversation->title;

            if ($title === null) {
                $title = $conversation->participants
                    ->where('id', '!=', $user->id)
                    ->pluck('name')
                    ->implode(', ');
            }

            $lastMessage = $conversation->messages()
                ->latest('created_at')
                ->first();

            return [
                'id' => $conversation->id,
                'title' => $title ?: 'Percakapan',
                'is_group' => $conversation->is_group,
                'participants' => $conversation->participants->map(fn ($participant) => [
                    'id' => $participant->id,
                    'name' => $participant->name,
                ])->values()->all(),
                'last_message_preview' => $lastMessage?->body,
                'last_message_time' => $lastMessage?->created_at?->diffForHumans(),
            ];
        })->values()->all();
    }

    public function selectConversation(int $conversationId): void
    {
        $user = Auth::user();

        $conversation = $user->conversations()->findOrFail($conversationId);

        $this->activeConversationId = $conversationId;
        $this->loadMessages();

        $user->conversations()->updateExistingPivot($conversationId, ['last_read_at' => now()]);

        $this->dispatch('chat-subscribe', conversationId: $conversationId);
    }

    public function loadMessages(): void
    {
        if ($this->activeConversationId === null) {
            $this->messages = [];

            return;
        }

        $messages = Message::query()
            ->with('user:id,name')
            ->where('conversation_id', $this->activeConversationId)
            ->orderBy('created_at')
            ->get();

        $this->messages = $messages->map(fn (Message $message) => $this->formatMessage($message))->all();
    }

    public function sendMessage(): void
    {
        $this->validate([
            'messageBody' => ['required', 'string', 'max:2000'],
        ]);

        if ($this->activeConversationId === null) {
            return;
        }

        $user = Auth::user();

        $message = DB::transaction(function () use ($user) {
            $message = Message::create([
                'conversation_id' => $this->activeConversationId,
                'user_id' => $user->id,
                'body' => $this->messageBody,
            ]);

            $message->conversation()->update(['updated_at' => now()]);

            return $message->fresh(['user:id,name']);
        });

        $this->messageBody = '';

        $this->messages[] = $this->formatMessage($message);

        broadcast(new MessageSent($message))->toOthers();

        $this->loadConversations();

        $this->dispatch('chat-scroll-to-bottom');
    }

    #[On('chat-message-received')]
    public function appendIncomingMessage(array $payload): void
    {
        if ($this->activeConversationId === (int) ($payload['conversation_id'] ?? 0)) {
            $this->messages[] = [
                'id' => $payload['id'],
                'body' => $payload['body'],
                'created_at_for_humans' => Carbon::parse($payload['created_at'])->diffForHumans(),
                'is_mine' => false,
                'user' => $payload['user'],
            ];

            $this->dispatch('chat-scroll-to-bottom');
        }

        $this->loadConversations();
    }

    public function render()
    {
        return view('livewire.chat.chat-board');
    }

    protected function formatMessage(Message $message): array
    {
        $user = Auth::user();

        return [
            'id' => $message->id,
            'body' => $message->body,
            'created_at_for_humans' => $message->created_at?->diffForHumans(),
            'is_mine' => $message->user_id === $user->id,
            'user' => [
                'id' => $message->user?->id,
                'name' => $message->user?->name,
            ],
        ];
    }
}
