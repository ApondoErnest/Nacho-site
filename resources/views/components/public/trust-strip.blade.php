@props([
    'items' => null,
])

@php
    $items = $items ?? [
        __('components.trust.approved_centers'),
        __('components.trust.modern_equipment'),
        __('components.trust.trained_inspectors'),
        __('components.trust.clear_tariffs'),
        __('components.trust.road_safety'),
    ];
@endphp

<section {{ $attributes->class(['rounded-2xl border border-nacho-dark/10 bg-white py-6 shadow-sm']) }}>
    <ul class="nacho-container grid grid-cols-2 gap-4 text-center sm:grid-cols-3 lg:grid-cols-5 lg:gap-6">
        @foreach ($items as $item)
            <li class="flex flex-col items-center gap-2 px-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-nacho-primary/10 text-nacho-primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                <span class="text-sm font-semibold text-nacho-dark">{{ $item }}</span>
            </li>
        @endforeach
    </ul>
</section>
