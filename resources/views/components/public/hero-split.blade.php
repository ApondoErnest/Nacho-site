@props([
    'title' => null,
    'subtitle' => null,
    'eyebrow' => null,
])

@php
    $title = $title ?? __('components.hero.title');
    $subtitle = $subtitle ?? __('components.hero.subtitle');
    $eyebrow = $eyebrow ?? __('components.hero.eyebrow');
    $heroImages = collect(range(1, 4))
        ->map(fn ($index) => "images/hero-inspection-bay-{$index}.png")
        ->filter(fn ($path) => file_exists(public_path($path)))
        ->map(fn ($path) => asset($path))
        ->values();
    $stats = __('components.hero_stats');
    $statIcons = ['car-front', 'user-check', 'shield-plus', 'circle-check', 'star'];
@endphp

<section
    {{ $attributes->class(['hero-showcase relative isolate bg-nacho-dark text-white']) }}
    x-data="{
        active: 0,
        slides: @js($heroImages),
        init() {
            if (this.slides.length > 1) {
                setInterval(() => {
                    this.active = (this.active + 1) % this.slides.length;
                }, 5500);
            }
        }
    }"
>
    <div class="absolute inset-0 -z-10 overflow-hidden">
        @foreach ($heroImages as $index => $image)
            <img
                src="{{ $image }}"
                alt=""
                class="absolute inset-0 h-full w-full object-cover transition-opacity duration-1000"
                x-show="active === {{ $index }}"
                x-transition.opacity
                @if ($index === 0) loading="eager" fetchpriority="high" @else loading="lazy" @endif
            />
        @endforeach
        <div class="absolute inset-0 bg-gradient-to-r from-[#071016]/95 via-[#071016]/68 to-[#071016]/12"></div>
        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-[#071016] to-transparent"></div>
    </div>

    <div class="nacho-container relative py-14 sm:py-16 lg:py-20">
        <div class="grid min-h-[34rem] items-center gap-10 lg:grid-cols-[minmax(0,0.95fr)_minmax(24rem,0.9fr)]">
            <div class="max-w-2xl">
                <p class="inline-flex items-center gap-2 text-sm font-extrabold uppercase tracking-wide text-nacho-primary sm:text-base">
                    <x-lucide-map-pin class="h-5 w-5" aria-hidden="true" />
                    {{ $eyebrow }}
                </p>

                <h1 class="mt-5 text-4xl font-extrabold leading-tight tracking-normal text-white sm:text-5xl lg:text-6xl">
                    {{ $title }}
                </h1>

                @if ($subtitle)
                    <p class="mt-6 max-w-xl text-base font-medium leading-8 text-white/85 sm:text-lg">
                        {{ $subtitle }}
                    </p>
                @endif

                @if (isset($actions))
                    <div class="mt-9 flex flex-col gap-4 sm:flex-row sm:items-center">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="nacho-container hero-stat-shell">
        <div class="hero-stat-strip">
            @foreach ($stats as $stat)
                @php
                    $statIcon = $statIcons[$loop->index] ?? 'circle-check';
                @endphp
                <div class="hero-stat-item">
                    <span class="hero-stat-icon" aria-hidden="true">
                        <x-dynamic-component :component="'lucide-' . $statIcon" />
                    </span>
                    <span>
                        <span class="hero-stat-value">{{ $stat['value'] }}</span>
                        <span class="hero-stat-label">{{ $stat['label'] }}</span>
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</section>
