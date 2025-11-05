<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Panel extends Component
{
    public ?int $activeConversationId = null;

    public string $message = '';

    public function mount(): void
    {
        $firstConversationId = $this->conversations->first()?->id;
        $this->activeConversationId = $this->activeConversationId ?? $firstConversationId;

        if ($this->activeConversationId !== null) {
            $this->markConversationAsRead($this->activeConversationId);
        }
    }

    public function selectConversation(int $conversationId): void
    {
        if (! $this->user()->conversations()->whereKey($conversationId)->exists()) {
            return;
        }

        $this->activeConversationId = $conversationId;
        $this->markConversationAsRead($conversationId);
    }

    public function sendMessage(): void
    {
        $conversation = $this->resolveConversation($this->activeConversationId);

        if ($conversation === null) {
            return;
        }

        $validated = $this->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($conversation, $validated): void {
            $conversation->messages()->create([
                'user_id' => $this->user()->id,
                'body' => trim($validated['message']),
            ]);

            $conversation->touch();
            $this->markConversationAsRead($conversation->id);
        });

        $this->message = '';
    }

    public function refreshConversations(): void
    {
        if ($this->activeConversationId !== null) {
            $this->markConversationAsRead($this->activeConversationId);
        }
    }

    public function getConversationsProperty()
    {
        return $this->user()->conversations()
            ->with([
                'messages' => fn (Builder $query) => $query->latest()->limit(1)->with('user:id,name'),
                'participants' => fn (Builder $query) => $query->select('users.id', 'users.name', 'users.role'),
            ])
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (Conversation $conversation) {
                $conversation->setRelation(
                    'messages',
                    $conversation->messages->sortByDesc('created_at')->values()
                );

                return $conversation;
            });
    }

    public function getActiveConversationProperty(): ?Conversation
    {
        if ($this->activeConversationId === null) {
            return null;
        }

        $conversation = $this->user()->conversations()
            ->with([
                'messages' => fn (Builder $query) => $query->latest()->limit(50)->with('user:id,name,role'),
                'participants' => fn (Builder $query) => $query->select('users.id', 'users.name', 'users.role'),
            ])
            ->find($this->activeConversationId);

        if ($conversation === null) {
            return null;
        }

        $conversation->setRelation(
            'messages',
            $conversation->messages->sortBy('created_at')->values()
        );

        return $conversation;
    }

    public function render()
    {
        return view('livewire.chat.panel');
    }

    private function resolveConversation(?int $conversationId): ?Conversation
    {
        if ($conversationId === null) {
            return null;
        }

        return $this->user()->conversations()->whereKey($conversationId)->first();
    }

    private function markConversationAsRead(int $conversationId): void
    {
        $conversation = $this->resolveConversation($conversationId);

        if ($conversation === null) {
            return;
        }

        $conversation->participants()->updateExistingPivot($this->user()->id, [
            'last_read_at' => now(),
        ]);
    }

    private function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
