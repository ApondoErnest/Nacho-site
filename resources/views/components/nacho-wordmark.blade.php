@props(['tagline' => false, 'variant' => 'default'])

@php
    $nameClass = match ($variant) {
        'light' => 'text-white',
        default => 'text-nacho-primary',
    };
    $taglineClass = match ($variant) {
        'light' => 'text-white/70',
        default => 'text-nacho-dark/70',
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex flex-col']) }}>
    <span @class(['text-2xl font-bold tracking-tight sm:text-3xl', $nameClass])>
        NACHO
    </span>
    @if ($tagline)
        <span @class(['mt-1 text-xs font-medium uppercase tracking-wider sm:text-sm', $taglineClass])>
            Vehicle Inspection
        </span>
    @endif
</div>
