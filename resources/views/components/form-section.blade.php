@props(['submit' => null])

<section {{ $attributes }}>
    <form @if ($submit) wire:submit.prevent="{{ $submit }}" @endif>
        <div class="row g-4">
            <div class="col-12 col-lg-4">
                <div class="mb-3">
                    @isset($title)
                        <h5 class="mb-1">{{ $title }}</h5>
                    @endisset

                    @isset($description)
                        <p class="text-muted small mb-0">{{ $description }}</p>
                    @endisset
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="row g-3">
                    {{ $form }}
                </div>

                @isset($actions)
                    <div class="d-flex justify-content-end align-items-center gap-2 mt-4">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        </div>
    </form>
</section>
