@props(['submit' => null])

<section {{ $attributes }}>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @isset($title)
                <h5 class="card-title">{{ $title }}</h5>
            @endisset

            @isset($description)
                <p class="card-subtitle text-muted small mb-3">{{ $description }}</p>
            @endisset

            <div class="mb-3">
                {{ $content ?? $slot }}
            </div>

            @isset($footer)
                <div @class(['d-flex justify-content-end gap-2'])>
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</section>
