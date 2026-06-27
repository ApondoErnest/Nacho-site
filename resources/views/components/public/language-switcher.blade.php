@props(['variant' => 'dark'])

@php
    $isDark = $variant === 'dark';
    $activeClass = $isDark ? 'font-semibold text-white' : 'font-semibold text-nacho-primary';
    $inactiveClass = $isDark ? 'text-white/70 hover:text-white' : 'text-nacho-dark/60 hover:text-nacho-dark';
    $separatorClass = $isDark ? 'text-white/40' : 'text-nacho-dark/30';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-1.5 text-sm']) }} role="navigation" aria-label="{{ __('navigation.language_navigation') }}">
    <a
        href="{{ route('language.switch', 'fr') }}"
        lang="fr"
        hreflang="fr"
        class="{{ app()->getLocale() === 'fr' ? $activeClass : $inactiveClass }}"
        @if (app()->getLocale() === 'fr') aria-current="true" @endif
    >
        FR
    </a>
    <span class="{{ $separatorClass }}" aria-hidden="true">|</span>
    <a
        href="{{ route('language.switch', 'en') }}"
        lang="en"
        hreflang="en"
        class="{{ app()->getLocale() === 'en' ? $activeClass : $inactiveClass }}"
        @if (app()->getLocale() === 'en') aria-current="true" @endif
    >
        EN
    </a>
</div>
