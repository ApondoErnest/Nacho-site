@props([
    'title',
    'description' => null,
    'href' => null,
    'linkLabel' => null,
])

<div {{ $attributes->class(['flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="max-w-3xl">
        <h2 class="text-nacho-dark">{{ $title }}</h2>
        @if ($description)
            <p class="mt-2 text-base text-nacho-dark/75">{{ $description }}</p>
        @endif
    </div>
    @if ($href && $linkLabel)
        <a href="{{ $href }}" class="shrink-0 text-sm font-semibold text-nacho-primary hover:text-nacho-primary-dark">
            {{ $linkLabel }} →
        </a>
    @endif
</div>
