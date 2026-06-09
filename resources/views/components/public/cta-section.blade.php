@props([
    'title' => null,
    'text' => null,
    'buttonLabel' => null,
    'buttonUrl' => null,
    'secondaryLabel' => null,
    'secondaryUrl' => null,
    'tertiaryLabel' => null,
    'tertiaryUrl' => null,
    'variant' => 'primary',
])

@php
    $title = $title ?? __('components.cta.default_title');
    $text = $text ?? __('components.cta.default_text');
    $buttonLabel = $buttonLabel ?? __('navigation.book');
    $buttonUrl = $buttonUrl ?? route('book-inspection');
    $isDark = $variant === 'dark';
@endphp

<section
    {{ $attributes->class([
        'relative overflow-hidden rounded-2xl px-6 py-10 shadow-lg sm:px-10 sm:py-14',
        'bg-gradient-to-br from-nacho-dark via-nacho-dark to-nacho-primary/40 text-white' => $isDark,
        'bg-gradient-to-br from-nacho-primary to-nacho-primary-dark text-white' => ! $isDark,
    ]) }}
>
    @if ($isDark)
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(181,71,31,0.35),transparent_55%)]" aria-hidden="true"></div>
    @endif
    <div class="relative mx-auto max-w-3xl text-center">
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
            @if ($tertiaryLabel && $tertiaryUrl)
                <a href="{{ $tertiaryUrl }}" class="inline-flex items-center text-sm font-semibold text-white underline-offset-2 hover:underline">
                    {{ $tertiaryLabel }}
                </a>
            @endif
        </div>
    </div>
</section>
