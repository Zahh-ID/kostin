<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Percakapan') }}</h1>
            <small class="text-muted">{{ __('Chat real-time antara tenant, owner, dan admin.') }}</small>
        </div>
    </x-slot>

    <div class="container-fluid">
        <livewire:chat.panel />
    </div>
</x-app-layout>
