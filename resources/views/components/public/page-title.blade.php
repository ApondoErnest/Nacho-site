@props([
    'title',
    'description' => null,
])

<div {{ $attributes->class(['']) }}>
    @if (isset($breadcrumb))
        <nav class="mb-3 text-sm text-nacho-dark/60" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-1.5">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-nacho-primary">{{ __('navigation.home') }}</a>
                </li>
                <li aria-hidden="true" class="text-nacho-dark/40">/</li>
                {{ $breadcrumb }}
            </ol>
        </nav>
    @endif

    <h1 class="text-nacho-dark">{{ $title }}</h1>

    @if ($description)
        <p class="mt-3 max-w-3xl text-lg text-nacho-dark/75">{{ $description }}</p>
    @endif
</div>
