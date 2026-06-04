@props([
    'steps' => null,
])

@php
    $steps = $steps ?? [
        __('components.process.step_1'),
        __('components.process.step_2'),
        __('components.process.step_3'),
        __('components.process.step_4'),
        __('components.process.step_5'),
    ];
@endphp

<ol {{ $attributes->class(['grid gap-6 sm:grid-cols-2 lg:grid-cols-5']) }}>
    @foreach ($steps as $index => $step)
        <li class="relative rounded-2xl bg-white p-5 shadow-sm ring-1 ring-nacho-dark/10">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-nacho-primary text-sm font-bold text-white">
                {{ $index + 1 }}
            </span>
            <p class="mt-3 text-sm font-medium leading-relaxed text-nacho-dark">{{ $step }}</p>
        </li>
    @endforeach
</ol>
