@props([
    'context' => 'nav',
    'tagline' => false,
])

@php
    $logoPath = config('branding.logo', 'images/reusable/logo.png');
    $hasLogo = file_exists(public_path($logoPath));

    // Lockup is about 2:1 (mark plus wordmark).
    $boxClass = match ($context) {
        'nav' => 'h-14 w-[7rem] sm:h-16 sm:w-[8rem] lg:h-[4.75rem] lg:w-[9.5rem]',
        'footer' => 'h-11 w-[8rem] sm:h-12 sm:w-[9rem] xl:h-16 xl:w-[11rem]',
        'auth' => 'h-24 w-[12rem] sm:h-28 sm:w-[14rem]',
        'sm' => 'h-12 w-[6rem]',
        default => 'h-16 w-[8rem]',
    };
@endphp

@if ($hasLogo)
    <span
        {{ $attributes->class([$boxClass, 'inline-flex shrink-0 items-center justify-start']) }}
        role="img"
        aria-label="{{ __(config('branding.logo_alt')) }}"
    >
        <x-public.optimized-image
            :path="$logoPath"
            alt=""
            class="block max-h-full w-auto max-w-full object-contain object-left"
            width="1774"
            height="887"
            decoding="async"
            :fetchpriority="$context === 'nav' ? 'high' : null"
            :loading="$context === 'nav' ? 'eager' : 'lazy'"
        />
    </span>
@else
    <x-nacho-wordmark :tagline="$tagline" {{ $attributes }} />
@endif
