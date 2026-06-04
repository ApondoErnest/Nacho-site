@props([
    'category',
    'vehicleType',
    'price',
    'validity' => null,
    'documents' => null,
    'notes' => null,
    'bookUrl' => null,
])

<article {{ $attributes->class(['card-nacho p-5 lg:hidden']) }}>
    <p class="text-xs font-bold uppercase tracking-wide text-nacho-primary">{{ $category }}</p>
    <h3 class="mt-1 text-lg font-bold text-nacho-dark">{{ $vehicleType }}</h3>
    <p class="mt-2 text-2xl font-bold text-nacho-primary">{{ $price }}</p>

    @if ($validity)
        <p class="mt-3 text-sm text-nacho-dark/75">
            <span class="font-semibold">{{ __('components.tariff.validity') }}:</span> {{ $validity }}
        </p>
    @endif
    @if ($documents)
        <p class="mt-2 text-sm text-nacho-dark/75">
            <span class="font-semibold">{{ __('components.tariff.documents') }}:</span> {{ $documents }}
        </p>
    @endif
    @if ($notes)
        <p class="mt-2 text-sm text-nacho-dark/60">{{ $notes }}</p>
    @endif

    <a href="{{ $bookUrl ?? route('book-inspection') }}" class="btn-nacho-primary mt-4 inline-flex w-full justify-center text-sm">
        {{ __('components.tariff.book') }}
    </a>
</article>
