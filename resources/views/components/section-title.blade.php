@props(['title' => null, 'description' => null])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    @if ($title)
        <h5 class="mb-1">{{ $title }}</h5>
    @endif

    @if ($description)
        <p class="text-muted small mb-0">{{ $description }}</p>
    @endif

    {{ $slot }}
</div>
