@props(['id' => null, 'maxWidth' => null])

@php
    $id = $id ?? md5($attributes->wire('model') ?? uniqid());
@endphp

<x-modal :name="$id" :max-width="$maxWidth" {{ $attributes }} x-data="{ show: false }" x-on:open-modal.window="$event.detail == '{{ $id }}' ? show = true : null" x-on:close-modal.window="$event.detail == '{{ $id }}' ? show = false : null">
    <div class="px-6 py-4">
        <div class="text-lg font-medium text-gray-900">
            {{ $title ?? '' }}
        </div>

        <div class="mt-4 text-sm text-gray-600">
            {{ $content ?? $slot }}
        </div>
    </div>

    @isset($footer)
        <div class="px-6 py-4 bg-gray-100 text-right">
            {{ $footer }}
        </div>
    @endisset
</x-modal>
