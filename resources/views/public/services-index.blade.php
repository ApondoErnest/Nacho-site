@extends('layouts.public')

@section('title', __('navigation.services'))

@section('content')
    @php
        $serviceItems = collect($serviceItems ?? config('home.services', []));
        $secondaryServices = $serviceItems->reject(fn (array $service) => $service['key'] === 'periodic')->values();
        $heroProofItems = [
            ['icon' => 'scan-search', 'title' => __('home.services.hero.proof.advanced_testing')],
            ['icon' => 'user-check', 'title' => __('home.services.hero.proof.experienced_technicians')],
            ['icon' => 'badge-check', 'title' => __('home.services.hero.proof.transparent_process')],
            ['icon' => 'hand-heart', 'title' => __('home.services.hero.proof.customer_care')],
        ];
        $inspectionCheckIcons = [
            'brake_performance' => 'disc-3',
            'emission_compliance' => 'cloud',
            'side_slip_alignment' => 'move-horizontal',
            'suspension_balance' => 'waves-vertical',
            'headlamp_precision' => 'lamp',
            'engine_indicators' => 'cog',
            'tyre_safety' => 'circle-gauge',
            'visual_elements' => 'clipboard-check',
        ];
        $lifecycleStepIcons = [
            'select_service' => 'calendar-days',
            'quick_book' => 'calendar-check',
            'arrival_checkin' => 'car-front',
            'technical_testing' => 'wrench',
            'report_delivery' => 'file-check-2',
        ];
        $resultIcons = [
            'accepted' => 'circle-check',
            'suspended' => 'circle-alert',
            'refused' => 'circle-x',
        ];
    @endphp

    <section class="services-hero" aria-labelledby="services-hero-title">
        <img
            src="{{ asset('images/here-services-1.png') }}"
            alt=""
            class="services-hero-image"
            loading="eager"
            fetchpriority="high"
        />
        <div class="services-hero-overlay" aria-hidden="true"></div>

        <div class="services-hero-inner">
            <div class="services-hero-copy">
                <p class="services-hero-kicker">{{ __('home.services.hero.eyebrow') }}</p>
                <h1 id="services-hero-title" class="services-hero-title">
                    {{ __('home.services.hero.title_line_1') }}<br>
                    {{ __('home.services.hero.title_line_2') }}
                </h1>
                <span class="services-hero-rule" aria-hidden="true"></span>
                <p class="services-hero-text">{{ __('home.services.hero.subtitle') }}</p>

                <div class="services-proof-grid" aria-label="{{ __('home.services.hero.proof_label') }}">
                    @foreach ($heroProofItems as $item)
                        <div class="services-proof-card">
                            <span class="services-proof-icon" aria-hidden="true">
                                <x-dynamic-component :component="'lucide-' . $item['icon']" />
                            </span>
                            <span>{{ $item['title'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="services-showcase-section" aria-label="{{ __('home.services.title') }}">
        <div class="services-showcase-grid">
            <article id="periodic-inspection" class="services-featured-card">
                <img
                    src="{{ asset('images/hero-services-2.png') }}"
                    alt=""
                    class="services-featured-image"
                    loading="lazy"
                />
                <div class="services-featured-shade" aria-hidden="true"></div>

                <div class="services-featured-content">
                    <span class="services-featured-icon" aria-hidden="true">
                        <x-lucide-car-front />
                    </span>

                    <h2>{{ __('home.services.periodic.title') }}</h2>
                    <p>{{ __('home.services.periodic.long_description') }}</p>

                    <ul class="services-featured-checks" aria-label="{{ __('home.services.featured.checks_label') }}">
                        @foreach (__('home.services.featured.checks') as $check)
                            <li>
                                <x-lucide-circle-check aria-hidden="true" />
                                <span>{{ $check }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="services-featured-actions">
                        <a href="{{ route('book-inspection') }}" class="services-featured-button services-featured-button--primary">
                            <span>{{ __('components.service.book_service') }}</span>
                            <x-lucide-arrow-right aria-hidden="true" />
                        </a>
                        <a href="{{ route('inspection-process') }}" class="services-featured-button services-featured-button--secondary">
                            <span>{{ __('home.services.featured.process_label') }}</span>
                            <x-lucide-eye aria-hidden="true" />
                        </a>
                    </div>
                </div>
            </article>

            <div class="services-list">
                @foreach ($secondaryServices as $service)
                    <a
                        id="{{ $service['slug'] }}"
                        href="{{ ($service['bookable'] ?? true) ? route('book-inspection') : route('contact') }}"
                        class="services-list-card"
                    >
                        <span class="services-list-icon" aria-hidden="true">
                            <x-dynamic-component :component="'lucide-' . $service['icon']" />
                        </span>
                        <span class="services-list-copy">
                            <span class="services-list-title">{{ $service['title'] ?? __('home.services.' . $service['key'] . '.title') }}</span>
                            <span class="services-list-text">{{ $service['description'] ?? __('home.services.' . $service['key'] . '.description') }}</span>
                        </span>
                        <span class="services-list-arrow" aria-hidden="true">
                            <x-lucide-arrow-right />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="services-checks-section" aria-labelledby="services-checks-title">
        <div class="services-checks-inner">
            <div class="services-checks-heading">
                <h2 id="services-checks-title">{{ __('home.services.inspection_checks.title') }}</h2>
                <span aria-hidden="true"></span>
            </div>

            <div class="services-checks-grid">
                @foreach (__('home.services.inspection_checks.items') as $check)
                    <article class="services-check-card">
                        <span class="services-check-icon" aria-hidden="true">
                            <x-dynamic-component :component="'lucide-' . ($inspectionCheckIcons[$check['key']] ?? 'clipboard-check')" />
                        </span>
                        <h3>{{ $check['title'] }}</h3>
                        <p>{{ $check['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="services-lifecycle-section" aria-labelledby="services-lifecycle-title">
        <div class="services-lifecycle-panel">
            <div class="services-lifecycle-flow">
                <div class="services-dark-heading">
                    <h2 id="services-lifecycle-title">
                        <span>{{ __('home.services.lifecycle.title_prefix') }}</span>
                        <strong>{{ __('home.services.lifecycle.title_highlight') }}</strong>
                        @if (__('home.services.lifecycle.title_suffix') !== '')
                            <span>{{ __('home.services.lifecycle.title_suffix') }}</span>
                        @endif
                    </h2>
                    <span aria-hidden="true"></span>
                </div>

                <ol class="services-lifecycle-steps">
                    @foreach (__('home.services.lifecycle.steps') as $index => $step)
                        <li class="services-lifecycle-step">
                            <span class="services-lifecycle-number">{{ $index + 1 }}</span>
                            <span class="services-lifecycle-icon" aria-hidden="true">
                                <x-dynamic-component :component="'lucide-' . ($lifecycleStepIcons[$step['key']] ?? 'clipboard-check')" />
                            </span>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['text'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="services-lifecycle-divider" aria-hidden="true"></div>

            <div class="services-result-summary">
                <div class="services-dark-heading services-dark-heading--result">
                    <h2>
                        <span>{{ __('home.services.result_summary.title_prefix') }}</span>
                        <strong>{{ __('home.services.result_summary.title_highlight') }}</strong>
                    </h2>
                    <span aria-hidden="true"></span>
                </div>

                <div class="services-result-list">
                    @foreach (__('home.services.result_summary.items') as $item)
                        <article class="services-result-row services-result-row--{{ $item['key'] }}">
                            <span class="services-result-icon" aria-hidden="true">
                                <x-dynamic-component :component="'lucide-' . ($resultIcons[$item['key']] ?? 'circle-check')" />
                            </span>
                            <h3>{{ $item['title'] }}</h3>
                            <p>{{ $item['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="services-booking-cta-section" aria-labelledby="services-booking-cta-title">
        <div class="services-booking-cta">
            <img
                src="{{ asset('images/advert-about.png') }}"
                alt=""
                class="services-booking-cta-image"
                loading="lazy"
            />
            <div class="services-booking-cta-overlay" aria-hidden="true"></div>

            <div class="services-booking-cta-content">
                <h2 id="services-booking-cta-title">
                    <span>{{ __('home.services.cta.title_prefix') }}</span>
                    <strong>{{ __('home.services.cta.title_highlight') }}</strong>
                </h2>
                <p>{{ __('home.services.cta.text') }}</p>

                <div class="services-booking-cta-actions">
                    <a href="{{ route('book-inspection') }}" class="services-booking-cta-button">
                        <span>{{ __('home.services.cta.book') }}</span>
                        <x-lucide-arrow-right aria-hidden="true" />
                    </a>
                    <span class="services-booking-cta-separator" aria-hidden="true"></span>
                    <a href="{{ route('tariffs') }}" class="services-booking-cta-link">
                        <span>{{ __('home.services.cta.tariffs') }}</span>
                        <x-lucide-arrow-right aria-hidden="true" />
                    </a>
                    <a href="{{ route('contact') }}" class="services-booking-cta-contact">
                        <span>{{ __('home.services.cta.contact') }}</span>
                        <x-lucide-arrow-right aria-hidden="true" />
                    </a>
                </div>
            </div>

            <div class="services-booking-cta-mark" aria-hidden="true">
                <x-lucide-shield-check />
            </div>
        </div>
    </section>
@endsection
