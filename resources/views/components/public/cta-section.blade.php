@props([
    'title' => null,
    'text' => null,
    'buttonLabel' => null,
    'buttonUrl' => null,
    'secondaryLabel' => null,
    'secondaryUrl' => null,
])

@php
    $title = $title ?? __('components.cta.default_title');
    $text = $text ?? __('components.cta.default_text');
    $buttonLabel = $buttonLabel ?? __('navigation.book');
    $buttonUrl = $buttonUrl ?? route('book-inspection');
@endphp

<section {{ $attributes->class(['rounded-2xl bg-gradient-to-br from-nacho-primary to-nacho-primary-dark px-6 py-10 text-white shadow-lg sm:px-10 sm:py-12']) }}>
    <div class="mx-auto max-w-3xl text-center">
        <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ $title }}</h2>
        @if ($text)
            <p class="mt-3 text-base text-white/90 sm:text-lg">{{ $text }}</p>
        @endif
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ $buttonUrl }}" class="inline-flex items-center rounded-full bg-white px-6 py-2.5 text-sm font-semibold text-nacho-primary shadow-md transition hover:bg-nacho-cream">
                {{ $buttonLabel }}
            </a>
            @if ($secondaryLabel && $secondaryUrl)
                <a href="{{ $secondaryUrl }}" class="inline-flex items-center rounded-full border border-white/40 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">
                    {{ $secondaryLabel }}
                </a>
            @endif
        </div>
    </div>
</section>
