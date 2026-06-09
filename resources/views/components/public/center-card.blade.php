@props([
    'name',
    'city',
    'status' => 'operational',
    'landmark' => null,
    'address' => null,
    'vehicleCategories' => null,
    'href' => '#',
    'imageUrl' => null,
])

@php
    $isOperational = $status === 'operational';
@endphp

<article {{ $attributes->class(['card-nacho flex flex-col']) }}>
    <div class="aspect-[16/10] bg-nacho-cream">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="" class="h-full w-full object-cover" loading="lazy" />
        @else
            <div class="flex h-full items-center justify-center text-nacho-dark/25">
                <x-lucide-building-2 class="h-16 w-16" aria-hidden="true" />
            </div>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex flex-wrap items-start justify-between gap-2">
            <div>
                <h3 class="text-lg font-bold text-nacho-dark">
                    <a href="{{ $href }}" class="hover:text-nacho-primary">{{ $name }}</a>
                </h3>
                <p class="text-sm text-nacho-dark/60">{{ $city }}</p>
            </div>
            <span @class([$isOperational ? 'badge-operational' : 'badge-construction'])>
                {{ $isOperational ? __('components.center.operational') : __('components.center.under_construction') }}
            </span>
        </div>

        @if ($landmark)
            <p class="mt-3 text-sm text-nacho-dark/75">
                <span class="font-semibold text-nacho-dark">{{ __('components.center.landmark') }}:</span>
                {{ $landmark }}
            </p>
        @endif

        @if ($address)
            <p class="mt-2 text-sm text-nacho-dark/70">{{ $address }}</p>
        @endif

        @if ($vehicleCategories)
            <p class="mt-3 text-sm text-nacho-dark/75">
                <span class="font-semibold text-nacho-dark">{{ __('components.center.vehicle_categories') }}:</span>
                {{ $vehicleCategories }}
            </p>
        @endif

        @if (! $isOperational)
            <p class="mt-3 text-sm font-medium text-nacho-warning">{{ __('components.center.opening_notice') }}</p>
        @endif

        <div class="mt-5 flex flex-wrap gap-2">
            @if ($isOperational)
                <a href="{{ route('book-inspection') }}" class="btn-nacho-primary text-sm">{{ __('components.center.book_at_center') }}</a>
            @endif
            <a href="{{ $href }}" class="btn-nacho-secondary text-sm">{{ __('components.center.directions') }}</a>
        </div>
    </div>
</article>
