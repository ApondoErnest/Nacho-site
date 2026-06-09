@props([
    'benefits' => null,
])

@php
    $benefits = $benefits ?? __('home.why.benefits');
@endphp

<div {{ $attributes->class(['grid gap-6 sm:grid-cols-2 lg:grid-cols-3']) }}>
    @foreach ($benefits as $benefit)
        <article class="card-nacho p-6 transition-shadow hover:shadow-md">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-nacho-primary/10 text-nacho-primary" aria-hidden="true">
                <x-dynamic-component :component="'lucide-' . $benefit['icon']" class="h-6 w-6" />
            </span>
            <h3 class="mt-4 text-lg font-bold text-nacho-dark">{{ $benefit['title'] }}</h3>
            <p class="mt-2 text-sm leading-relaxed text-nacho-dark/75">{{ $benefit['text'] }}</p>
        </article>
    @endforeach
</div>
