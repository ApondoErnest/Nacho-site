@extends('layouts.public')

@section('title', __('about.meta_title'))

@section('content')
    @php
        $commitmentCards = [
            [
                'icon' => 'target',
                'title' => __('about.commitment.mission.title'),
                'body' => __('about.commitment.mission.body'),
            ],
            [
                'icon' => 'eye',
                'title' => __('about.commitment.vision.title'),
                'body' => __('about.commitment.vision.body'),
            ],
            [
                'icon' => 'shield-plus',
                'title' => __('about.commitment.values.title'),
                'values' => __('about.commitment.values.items'),
            ],
        ];
        $whyCards = __('about.why.cards');
        $centerCards = __('about.centers.cards');
    @endphp

    <section class="about-page-hero" aria-labelledby="about-page-hero-title">
        <img
            src="{{ asset('images/hero-about-page.png') }}"
            alt=""
            class="about-page-hero-image"
            loading="eager"
            fetchpriority="high"
        />
        <div class="about-page-hero-overlay" aria-hidden="true"></div>

        <div class="about-page-hero-inner">
            <div class="about-page-hero-copy">
                <p class="about-page-hero-eyebrow">{{ __('about.hero.eyebrow') }}</p>
                <h1 id="about-page-hero-title" class="about-page-hero-title">
                    {{ __('about.hero.title') }}
                </h1>
                <p class="about-page-hero-text">
                    {{ __('about.hero.subtitle') }}
                </p>
                <div class="about-page-hero-actions">
                    <a href="{{ route('book-inspection') }}" class="about-page-hero-action about-page-hero-action--primary">
                        {{ __('about.hero.cta_book') }}
                    </a>
                    <a href="{{ route('centers.index') }}" class="about-page-hero-action about-page-hero-action--secondary">
                        {{ __('about.hero.cta_centers') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="about-commitment-section" aria-labelledby="about-commitment-title">
        <div class="about-commitment-grid">
            <div class="about-commitment-copy">
                <h2 id="about-commitment-title" class="about-commitment-title">
                    {{ __('about.commitment.title') }}
                </h2>
                <span class="about-commitment-rule" aria-hidden="true"></span>

                <div class="about-commitment-text">
                    @foreach (__('about.commitment.paragraphs') as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>

            <div class="about-commitment-cards">
                @foreach ($commitmentCards as $card)
                    <article class="about-commitment-card">
                        <div class="about-commitment-card-icon" aria-hidden="true">
                            <x-dynamic-component :component="'lucide-' . $card['icon']" />
                        </div>

                        <div class="about-commitment-card-copy">
                            <h3>{{ $card['title'] }}</h3>

                            @if (isset($card['values']))
                                <ul class="about-commitment-values" aria-label="{{ $card['title'] }}">
                                    @foreach ($card['values'] as $value)
                                        <li>{{ $value }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p>{{ $card['body'] }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="about-why-section" aria-labelledby="about-why-title">
        <div class="about-why-inner">
            <h2 id="about-why-title" class="about-why-title">
                {{ __('about.why.title') }}
            </h2>
            <span class="about-why-rule" aria-hidden="true"></span>

            <div class="about-why-grid">
                @foreach ($whyCards as $card)
                    <article class="about-why-card">
                        <div class="about-why-card-icon" aria-hidden="true">
                            <x-dynamic-component :component="'lucide-' . $card['icon']" />
                        </div>

                        <h3>{{ $card['title'] }}</h3>
                        <p>{{ $card['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="about-centers-section" aria-labelledby="about-centers-title">
        <div class="about-centers-inner">
            <h2 id="about-centers-title" class="about-centers-title">
                {{ __('about.centers.title') }}
            </h2>

            <div class="about-centers-grid">
                @foreach ($centerCards as $card)
                    @php
                        $imageUrl = file_exists(public_path($card['image'])) ? asset($card['image']) : null;
                        $isOperational = $card['status'] === 'operational';
                    @endphp

                    <article @class(['about-center-card', 'about-center-card--coming' => ! $isOperational])>
                        @if ($isOperational)
                            <div class="about-center-photo">
                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $card['name'] }}" loading="lazy" />
                                @endif
                            </div>

                            <div class="about-center-body">
                                <h3>{{ $card['name'] }}</h3>
                                <p class="about-center-location">{{ $card['location'] }}</p>
                                <p class="about-center-status about-center-status--operational">
                                    <span aria-hidden="true"></span>
                                    {{ __('about.centers.operational') }}
                                </p>
                                <a href="{{ $card['href'] }}" class="about-center-map" target="_blank" rel="noopener">
                                    <x-lucide-map-pin aria-hidden="true" />
                                    {{ __('about.centers.maps') }}
                                </a>
                            </div>
                        @else
                            <div class="about-center-coming-visual">
                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="" loading="lazy" />
                                @endif
                            </div>

                            <div class="about-center-coming-body">
                                <h3>{{ $card['name'] }}</h3>
                                <p class="about-center-region">{{ $card['region'] }}</p>
                                <p class="about-center-coming-label">{{ __('about.centers.coming_soon') }}</p>
                                <p class="about-center-target">
                                    <strong>{{ __('about.centers.target_phase') }}</strong>
                                    <span>{{ $card['target'] }}</span>
                                </p>
                                <a href="{{ $card['href'] }}" class="about-center-details">
                                    {{ __('about.centers.details') }}
                                </a>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="about-team-strip-section" aria-label="{{ __('about.team_strip.label') }}">
        <div class="about-team-strip">
            <span class="about-team-strip-icon" aria-hidden="true">
                <x-lucide-users />
            </span>
            <p>
                <span>{{ __('about.team_strip.prefix') }}</span>
                <strong>{{ __('about.team_strip.highlight') }}</strong>
                <span>{{ __('about.team_strip.suffix') }}</span>
            </p>
        </div>
    </section>

    <section class="about-advert-section" aria-labelledby="about-advert-title">
        <div class="about-advert">
            <div class="about-advert-image-wrap">
                <img
                    src="{{ asset('images/advert-about.png') }}"
                    alt=""
                    class="about-advert-image"
                    loading="lazy"
                />
            </div>

            <div class="about-advert-content">
                <h2 id="about-advert-title">{{ __('about.advert.title') }}</h2>
                <p>{{ __('about.advert.text') }}</p>

                <div class="about-advert-actions">
                    <a href="{{ route('book-inspection') }}" class="about-advert-button about-advert-button--primary">
                        {{ __('about.advert.cta_book') }}
                    </a>
                    <a href="{{ route('contact') }}" class="about-advert-button about-advert-button--link">
                        <span>{{ __('about.advert.cta_contact') }}</span>
                        <x-lucide-arrow-right aria-hidden="true" />
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
