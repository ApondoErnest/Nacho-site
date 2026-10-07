@props([
    'image' => null,
])

@php
    $image = $image ?? 'images/homepage/about.png';
@endphp

<section {{ $attributes->class(['about-nacho-section']) }}>
    <div class="about-nacho-grid">
        <div class="about-nacho-copy">
            <p class="about-nacho-kicker">{{ __('home.about.eyebrow') }}</p>
            <h2 class="about-nacho-title">{{ __('home.about.title') }}</h2>
            <p class="about-nacho-text">{{ __('home.about.intro') }}</p>

            <ul class="about-nacho-list" aria-label="{{ __('home.about.list_label') }}">
                @foreach (__('home.about.points') as $point)
                    <li class="about-nacho-point">
                        <span class="about-nacho-check" aria-hidden="true">
                            <x-lucide-circle-check class="text-nacho-primary" />
                        </span>
                        <span>{{ $point }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="about-nacho-visual-wrap">
            <div class="about-nacho-visual">
                <x-public.optimized-image
                    :path="$image"
                    :alt="__('home.about.image_alt')"
                    class="about-nacho-image"
                    loading="lazy"
                />
                <div class="about-nacho-image-shade" aria-hidden="true"></div>
                <p class="about-nacho-image-text">{!! __('home.about.image_statement') !!}</p>

                <div class="about-nacho-count-card">
                    <span class="about-nacho-count-icon" aria-hidden="true">
                        <x-lucide-building-2 />
                    </span>
                    <span>
                        <span class="about-nacho-count-value">{{ __('home.about.count_value') }}</span>
                        <span class="about-nacho-count-label">{{ __('home.about.count_label') }}</span>
                    </span>
                </div>
            </div>

            <p class="about-nacho-status">
                <span>{{ __('home.about.status_operational') }}</span>
            </p>
        </div>
    </div>
</section>
