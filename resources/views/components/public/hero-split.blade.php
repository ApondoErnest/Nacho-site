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
        ->map(fn ($index) => "images/homepage/hero-{$index}.png")
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
        <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-[#071016] to-transparent sm:h-28 xl:h-40"></div>
    </div>

    <div class="nacho-container relative py-8 sm:py-10 xl:py-20">
        <div class="grid items-center gap-6 sm:gap-8 xl:min-h-[34rem] xl:grid-cols-[minmax(0,0.95fr)_minmax(24rem,0.9fr)] xl:gap-10">
            <div class="max-w-2xl">
                <p class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-wide text-nacho-primary sm:text-sm xl:text-base">
                    <x-lucide-map-pin class="h-4 w-4 xl:h-5 xl:w-5" aria-hidden="true" />
                    {{ $eyebrow }}
                </p>

                <h1 class="mt-3 text-3xl font-extrabold leading-tight tracking-normal text-white sm:mt-4 sm:text-4xl xl:mt-5 xl:text-6xl">
                    {{ $title }}
                </h1>

                @if ($subtitle)
                    <p class="mt-3 max-w-xl text-sm font-medium leading-6 text-white/85 sm:mt-4 sm:text-base sm:leading-7 xl:mt-6 xl:text-lg xl:leading-8">
                        {{ $subtitle }}
                    </p>
                @endif

                @if (isset($actions))
                    <div class="mt-5 flex flex-col gap-2.5 sm:mt-6 sm:flex-row sm:items-center sm:gap-3 xl:mt-9 xl:gap-4">
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
