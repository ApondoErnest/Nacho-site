@props([
    'context' => 'nav',
    'tagline' => false,
])

@php
    $logoPath = config('branding.logo', 'images/nacho-logo.png');
    $hasLogo = file_exists(public_path($logoPath));

    // Wide logo (~3:2). Boxes sized so the mark stays readable without crowding the nav.
    $boxClass = match ($context) {
        'nav' => 'h-20 w-max max-w-[18rem] sm:h-24 sm:max-w-[22rem] lg:h-28 lg:max-w-[26rem] xl:h-32 xl:max-w-[30rem]',
        'footer' => 'h-16 w-[13.5rem] sm:h-20 sm:w-[17rem]',
        'auth' => 'h-20 w-[15rem] sm:h-24 sm:w-[18.5rem]',
        'sm' => 'h-10 w-[8.5rem]',
        default => 'h-14 w-[12.75rem]',
    };
@endphp

@if ($hasLogo)
    <span
        {{ $attributes->class([$boxClass, 'inline-flex shrink-0 items-center justify-start']) }}
        role="img"
        aria-label="{{ __(config('branding.logo_alt')) }}"
    >
        <img
            src="{{ asset($logoPath) }}"
            alt=""
            class="block max-h-full w-auto max-w-full object-contain object-left"
            width="1024"
            height="682"
            decoding="async"
            @if ($context === 'nav') fetchpriority="high" @endif
        />
    </span>
@else
    <x-nacho-wordmark :tagline="$tagline" {{ $attributes }} />
@endif
