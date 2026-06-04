@props([
    'title' => null,
    'subtitle' => null,
    'slogan' => null,
    'compact' => false,
])

@php
    $title = $title ?? __('components.hero.title');
    $subtitle = $subtitle ?? __('components.hero.subtitle');
    $slogan = $slogan ?? __('components.slogan');
@endphp

<section {{ $attributes->class([
    'relative overflow-hidden rounded-2xl bg-nacho-dark text-white',
    'px-6 py-10 sm:px-10 sm:py-14' => ! $compact,
    'px-6 py-8 sm:px-8' => $compact,
]) }}>
    <div class="absolute inset-0 bg-gradient-to-br from-nacho-primary/20 via-transparent to-nacho-dark"></div>
    <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-nacho-primary/10 blur-3xl"></div>

    <div class="relative max-w-3xl">
        <p class="text-sm font-semibold uppercase tracking-wider text-nacho-primary sm:text-base">
            {{ $slogan }}
        </p>
        <h1 @class(['mt-3 font-bold tracking-tight text-white', 'text-3xl sm:text-4xl lg:text-5xl' => ! $compact, 'text-2xl sm:text-3xl' => $compact])>
            {{ $title }}
        </h1>
        @if ($subtitle)
            <p class="mt-4 max-w-2xl text-base leading-relaxed text-white/85 sm:text-lg">
                {{ $subtitle }}
            </p>
        @endif

        @if (isset($actions))
            <div class="mt-8 flex flex-wrap gap-3">
                {{ $actions }}
            </div>
        @endif
    </div>
</section>
