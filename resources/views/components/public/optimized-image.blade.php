@props([
    'path',
    'alt' => '',
    'loading' => 'lazy',
    'fetchpriority' => null,
    'decoding' => 'async',
])

@php
    $sources = \App\Support\PublicImage::sources($path);
@endphp

@if ($sources)
    <picture class="contents">
        @if ($sources['webp'])
            <source type="image/webp" srcset="{{ $sources['webp'] }}">
        @endif
        <img
            src="{{ $sources['src'] }}"
            alt="{{ $alt }}"
            loading="{{ $loading }}"
            decoding="{{ $decoding }}"
            @if ($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
            {{ $attributes->except(['path', 'alt', 'loading', 'fetchpriority', 'decoding']) }}
        />
    </picture>
@endif
