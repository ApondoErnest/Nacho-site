@props([
    'operationalOnly' => false,
])

@php
    $locale = app()->getLocale();
    $centers = collect(config('centers.centers', []));
    if ($operationalOnly) {
        $centers = $centers->where('status', 'operational');
    }
@endphp

<div {{ $attributes->class(['grid gap-6 md:grid-cols-2 xl:grid-cols-3']) }}>
    @foreach ($centers as $center)
        @php
            $suffix = $locale === 'fr'
                ? ($center['name_suffix_fr'] ?? '')
                : ($center['name_suffix_en'] ?? '');
            $displayName = trim($center['name'] . ' ' . $suffix);
            $hours = $locale === 'fr' ? ($center['hours_fr'] ?? null) : ($center['hours_en'] ?? null);
            $phonesLine = ! empty($center['phones']) ? implode(' · ', $center['phones']) : null;
            $href = $center['maps_url'] ?? route('centers.index');
        @endphp
        <x-public.center-card
            :name="$displayName"
            :city="$center['city']"
            :status="$center['status']"
            :landmark="$center['landmark'] ?? null"
            :address="$center['address'] ?? null"
            :opening-hours="$hours"
            :phones="$phonesLine"
            :href="$href"
            id="{{ $center['slug'] }}"
        />
    @endforeach
</div>
