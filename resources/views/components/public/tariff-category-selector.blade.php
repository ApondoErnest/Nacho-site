@props([
    'categories' => null,
])

@php
    $categories = $categories ?? config('home.tariff_categories', []);
@endphp

<div {{ $attributes->class(['flex flex-wrap justify-center gap-2']) }} role="tablist" aria-label="{{ __('home.tariffs.categories_label') }}">
    @foreach ($categories as $cat)
        <button
            type="button"
            @click="active = @js($cat['id'])"
            :class="active === @js($cat['id'])
                ? 'bg-nacho-primary text-white shadow-sm'
                : 'bg-white text-nacho-dark ring-1 ring-nacho-dark/15 hover:bg-nacho-cream'"
            class="rounded-full px-4 py-2 text-sm font-semibold transition-colors"
            role="tab"
            :aria-selected="(active === @js($cat['id'])).toString()"
        >
            {{ app()->getLocale() === 'fr' ? $cat['label_fr'] : $cat['label_en'] }}
        </button>
    @endforeach
</div>
