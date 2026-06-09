@php
    $operationalCenters = collect(config('centers.centers', []))
        ->filter(fn (array $center) => ($center['status'] ?? null) === 'operational')
        ->values();
    $operationalCount = $operationalCenters->count();

    $serviceLinks = [
        ['label' => __('footer.services.technical_inspection'), 'href' => route('services.index') . '#periodic-inspection'],
        ['label' => __('footer.services.emission_testing'), 'href' => route('services.index')],
        ['label' => __('footer.services.brake_testing'), 'href' => route('services.index')],
        ['label' => __('footer.services.engine_diagnostics'), 'href' => route('services.index')],
        ['label' => __('footer.services.tires_suspension'), 'href' => route('services.index')],
    ];

    $customerLinks = [
        ['label' => __('footer.customers.book'), 'href' => route('book-inspection')],
        ['label' => __('footer.customers.find_center'), 'href' => route('centers.index')],
        ['label' => __('footer.customers.process'), 'href' => route('inspection-process')],
        ['label' => __('footer.customers.prices'), 'href' => route('tariffs')],
        ['label' => __('footer.customers.faqs'), 'href' => route('contact')],
    ];

    $quickLinks = [
        ['label' => __('navigation.about'), 'href' => route('about')],
        ['label' => __('navigation.services'), 'href' => route('services.index')],
        ['label' => __('navigation.centers'), 'href' => route('centers.index')],
        ['label' => __('navigation.careers'), 'href' => route('careers.index')],
        ['label' => __('footer.news_updates'), 'href' => route('blog.index')],
    ];

    $primaryPhone = config('centers.headquarters.phone_primary');
    $primaryPhoneTel = config('centers.headquarters.phone_primary_tel');
    $hqEmail = config('centers.headquarters.email');
    $hqAddress = config('centers.headquarters.address');
    $socialIcons = ['facebook', 'instagram', 'twitter', 'message-circle'];
@endphp

<footer class="site-footer">
    <div class="site-footer-cta">
        <div class="site-footer-cta-icon" aria-hidden="true">
            <x-lucide-calendar-clock />
        </div>

        <div class="site-footer-cta-copy">
            <h2>{{ __('footer.ready_title') }}</h2>
            <p>{{ trans_choice('footer.ready_text', $operationalCount, ['count' => $operationalCount]) }}</p>
        </div>

        <div class="site-footer-cta-actions">
            <a href="{{ route('book-inspection') }}" class="site-footer-cta-button site-footer-cta-button--primary">
                <x-lucide-calendar-check aria-hidden="true" />
                {{ __('navigation.book') }}
            </a>
            <a href="{{ route('centers.index') }}" class="site-footer-cta-button">
                <x-lucide-map-pin aria-hidden="true" />
                {{ __('footer.find_center') }}
            </a>
            <a href="{{ route('contact') }}" class="site-footer-cta-button">
                <x-lucide-message-circle aria-hidden="true" />
                {{ __('footer.contact_us') }}
            </a>
        </div>
    </div>

    <div class="site-footer-heading-strip" aria-hidden="true">
        <span></span>
        <span>{{ __('footer.services_heading') }}</span>
        <span>{{ __('footer.customers_heading') }}</span>
        <span>{{ __('footer.quick_links') }}</span>
    </div>

    <div class="site-footer-main">
        <section class="site-footer-brand">
            <a href="{{ route('home') }}" class="site-footer-logo" aria-label="{{ __('branding.logo_alt') }}">
                <x-nacho-logo context="footer" />
            </a>
            <p>{{ __('footer.short_tagline') }}</p>
            <div class="site-footer-socials" aria-label="{{ __('footer.social_label') }}">
                @foreach ($socialIcons as $socialIcon)
                    <span class="site-footer-social" aria-hidden="true">
                        <x-dynamic-component :component="'lucide-' . $socialIcon" />
                    </span>
                @endforeach
            </div>
        </section>

        <section class="site-footer-column">
            <h2>{{ __('footer.services_heading') }}</h2>
            <ul>
                @foreach ($serviceLinks as $link)
                    <li><a href="{{ $link['href'] }}">{{ $link['label'] }}</a></li>
                @endforeach
            </ul>
        </section>

        <section class="site-footer-column">
            <h2>{{ __('footer.customers_heading') }}</h2>
            <ul>
                @foreach ($customerLinks as $link)
                    <li><a href="{{ $link['href'] }}">{{ $link['label'] }}</a></li>
                @endforeach
            </ul>

            <p class="site-footer-operational-title">{{ trans_choice('footer.operational_centers_title', $operationalCount, ['count' => $operationalCount]) }}</p>
            <ul class="site-footer-centers">
                @foreach ($operationalCenters as $center)
                    <li>
                        <a href="{{ route('centers.index') . '#' . $center['slug'] }}">{{ $center['name'] }}</a>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="site-footer-column site-footer-column--contact">
            <h2>{{ __('footer.quick_links') }}</h2>
            <ul>
                @foreach ($quickLinks as $link)
                    <li><a href="{{ $link['href'] }}">{{ $link['label'] }}</a></li>
                @endforeach
            </ul>

            <address class="site-footer-contact">
                <a href="tel:{{ $primaryPhoneTel }}">
                    <x-lucide-phone aria-hidden="true" />
                    {{ $primaryPhone }}
                </a>
                <a href="mailto:{{ $hqEmail }}">
                    <x-lucide-mail aria-hidden="true" />
                    {{ $hqEmail }}
                </a>
                <span>
                    <x-lucide-map-pin aria-hidden="true" />
                    {{ $hqAddress }}
                </span>
                <span>
                    <x-lucide-clock aria-hidden="true" />
                    {{ __(config('navigation.opening_hours_key')) }}
                </span>
            </address>
        </section>
    </div>

    <div class="site-footer-bottom">
        <p>{{ __('footer.copyright', ['year' => date('Y')]) }}</p>
        <nav aria-label="{{ __('footer.legal') }}">
            @foreach (config('navigation.legal') as $item)
                <a href="{{ route($item['route']) }}">{{ __($item['label']) }}</a>
            @endforeach
        </nav>
    </div>
</footer>
