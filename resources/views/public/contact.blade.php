@extends('layouts.public')

@section('title', __('contact.meta_title'))

@section('content')
    @php
        $locale = app()->getLocale();
        $contactActions = [
            [
                'icon' => 'calendar-days',
                'title' => __('contact.hero.actions.book.title'),
                'text' => __('contact.hero.actions.book.text'),
                'href' => route('book-inspection'),
            ],
            [
                'icon' => 'phone',
                'title' => __('contact.hero.actions.station.title'),
                'text' => __('contact.hero.actions.station.text'),
                'href' => '#contact-centers',
            ],
            [
                'icon' => 'messages-square',
                'title' => __('contact.hero.actions.enquiry.title'),
                'text' => __('contact.hero.actions.enquiry.text'),
                'href' => '#contact-form',
            ],
            [
                'icon' => 'building-2',
                'title' => __('contact.hero.actions.hq.title'),
                'text' => __('contact.hero.actions.hq.text'),
                'href' => '#contact-headquarters',
            ],
        ];
        $centerImages = [
            'nacho-yaounde' => 'images/center-nacho-yaounde.png',
            'nacho-nkwen-bamenda' => 'images/center-nacho-nkwen-bamenda.png',
            'nacho-mankon-bamenda' => 'images/center-nacho-nacho-bamenda.png',
        ];
        $centers = collect(config('centers.centers', []));
        $operationalCenters = $centers->where('status', 'operational')->values();
        $comingCenters = $centers->where('status', 'under_construction')->values();
        $approximateCityCoordinates = [
            'nacho-douala' => ['latitude' => 4.0511, 'longitude' => 9.7679],
            'nacho-kumba' => ['latitude' => 4.6363, 'longitude' => 9.4469],
        ];
        $mapCenters = $centers
            ->map(function (array $center) use ($locale, $approximateCityCoordinates) {
                $suffix = $locale === 'fr'
                    ? ($center['name_suffix_fr'] ?? '')
                    : ($center['name_suffix_en'] ?? '');
                $latitude = $center['latitude'] ?? ($approximateCityCoordinates[$center['slug']]['latitude'] ?? null);
                $longitude = $center['longitude'] ?? ($approximateCityCoordinates[$center['slug']]['longitude'] ?? null);

                if ($latitude === null || $longitude === null) {
                    return null;
                }

                return [
                    'slug' => $center['slug'],
                    'name' => trim($center['name'] . ' ' . $suffix),
                    'city' => $center['city'],
                    'region' => $center['region'],
                    'status' => $center['status'],
                    'status_label' => $center['status'] === 'operational'
                        ? __('components.center.operational')
                        : __('components.center.opening_badge'),
                    'address' => $center['address'] ?: trim($center['city'] . ', ' . $center['region']),
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'is_approximate' => empty($center['latitude']) || empty($center['longitude']),
                    'approximate_label' => __('contact.centers.map.approximate'),
                ];
            })
            ->filter()
            ->values();
    @endphp

    <section class="contact-hero" aria-labelledby="contact-hero-title">
        <img
            src="{{ asset('images/hero-contacts.png') }}"
            alt=""
            class="contact-hero-image"
            loading="eager"
            fetchpriority="high"
        />
        <div class="contact-hero-overlay" aria-hidden="true"></div>

        <div class="contact-hero-inner">
            <div class="contact-hero-copy">
                <div class="contact-hero-brand" aria-label="NACHO Vehicle Inspection">
                    <span class="contact-hero-brand-mark" aria-hidden="true">
                        <x-lucide-shield-check />
                    </span>
                    <span class="contact-hero-brand-copy">
                        <span class="contact-hero-brand-name">NACHO</span>
                        <span class="contact-hero-brand-subtitle">Vehicle Inspection</span>
                    </span>
                </div>

                <h1 id="contact-hero-title" class="contact-hero-title">
                    {{ __('contact.hero.title') }}
                </h1>

                <p class="contact-hero-text">
                    {{ __('contact.hero.subtitle') }}
                </p>
            </div>

            <div class="contact-hero-actions" aria-label="{{ __('contact.hero.actions_label') }}">
                @foreach ($contactActions as $action)
                    <a href="{{ $action['href'] }}" class="contact-hero-card">
                        <span class="contact-hero-card-icon" aria-hidden="true">
                            <x-dynamic-component :component="'lucide-' . $action['icon']" />
                        </span>

                        <span class="contact-hero-card-copy">
                            <span class="contact-hero-card-title">{{ $action['title'] }}</span>
                            <span class="contact-hero-card-text">{{ $action['text'] }}</span>
                        </span>

                        <span class="contact-hero-card-arrow" aria-hidden="true">
                            <x-lucide-chevron-right />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section id="contact-centers" class="contact-centers-section" aria-labelledby="contact-centers-title">
        <div class="contact-centers-inner">
            <h2 id="contact-centers-title" class="contact-centers-title">
                {{ __('contact.centers.title') }}
            </h2>

            <div class="contact-centers-layout">
                <div class="contact-centers-list">
                    @foreach ($operationalCenters as $center)
                        @php
                            $isHeadquarters = $center['slug'] === 'nacho-mankon-bamenda';
                            $imagePath = $centerImages[$center['slug']] ?? null;
                            $imageUrl = $imagePath && file_exists(public_path($imagePath)) ? asset($imagePath) : null;
                            $hours = $locale === 'fr' ? ($center['hours_fr'] ?? null) : ($center['hours_en'] ?? null);
                            $phonesLine = implode(' / ', $center['phones'] ?? []);
                            $primaryPhone = $center['phones'][0] ?? null;
                            $phoneHref = $primaryPhone ? preg_replace('/[^\d+]/', '', $primaryPhone) : null;
                            $centerTitle = $isHeadquarters
                                ? $center['name'] . ' / ' . __('contact.centers.headquarters')
                                : $center['name'];
                        @endphp

                        <article
                            id="{{ $isHeadquarters ? 'contact-headquarters' : 'contact-center-' . $center['slug'] }}"
                            class="contact-center-card"
                        >
                            <div class="contact-center-thumb">
                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $center['name'] }}" loading="lazy" />
                                @endif
                            </div>

                            <div class="contact-center-card-body">
                                <h3>{{ $centerTitle }}</h3>

                                <ul class="contact-center-details" aria-label="{{ $centerTitle }}">
                                    <li>
                                        <x-lucide-map-pin aria-hidden="true" />
                                        <span>{{ $center['address'] }}, {{ $center['city'] }}</span>
                                    </li>

                                    @if ($isHeadquarters)
                                        <li>
                                            <x-lucide-map aria-hidden="true" />
                                            <span>{{ config('centers.headquarters.postal_box') }}</span>
                                        </li>
                                    @endif

                                    @if ($phonesLine)
                                        <li>
                                            <x-lucide-phone aria-hidden="true" />
                                            <a href="{{ $phoneHref ? 'tel:' . $phoneHref : '#' }}">{{ $phonesLine }}</a>
                                        </li>
                                    @endif

                                    @if (! empty($center['email']))
                                        <li>
                                            <x-lucide-mail aria-hidden="true" />
                                            <a href="mailto:{{ $center['email'] }}">{{ $center['email'] }}</a>
                                        </li>
                                    @endif

                                    @if ($hours)
                                        <li>
                                            <x-lucide-clock aria-hidden="true" />
                                            <span>{{ $hours }}</span>
                                        </li>
                                    @endif

                                    @if ($isHeadquarters)
                                        <li>
                                            <x-lucide-info aria-hidden="true" />
                                            <span>{{ __('contact.centers.hq_note') }}</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </article>
                    @endforeach

                    <div class="contact-coming-list" aria-label="{{ __('contact.centers.coming_label') }}">
                        @foreach ($comingCenters as $center)
                            <article class="contact-coming-card">
                                <span class="contact-coming-icon" aria-hidden="true">
                                    <x-lucide-building-2 />
                                </span>
                                <span class="contact-coming-copy">
                                    <span class="contact-coming-name">{{ $center['name'] }}</span>
                                    <span class="contact-coming-region">{{ $center['region'] }} {{ __('contact.centers.region') }}</span>
                                </span>
                                <span class="contact-coming-phase">{{ __('contact.centers.expansion_phase') }}</span>
                                <span class="contact-coming-date">{{ __('contact.centers.coming_before') }}</span>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="contact-centers-map-panel" aria-label="{{ __('contact.centers.map.label') }}">
                    <div
                        id="contact-centers-map"
                        class="contact-leaflet-map"
                        role="application"
                        aria-label="{{ __('contact.centers.map.label') }}"
                    ></div>

                    <div class="contact-centers-map-heading">
                        <span>{{ __('contact.centers.map.title') }}</span>
                        <x-lucide-layers aria-hidden="true" />
                    </div>

                    <p class="contact-map-loading" data-contact-map-loading>
                        {{ __('contact.centers.map.loading') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <script type="application/json" id="contact-centers-map-data">{!! json_encode($mapCenters, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) !!}</script>

    <section id="contact-form" class="contact-message-section" aria-labelledby="contact-form-title">
        <div class="contact-message-inner">
            <form action="#" method="POST" class="contact-message-form" novalidate>
                @csrf

                <div class="contact-message-heading">
                    <h2 id="contact-form-title">{{ __('contact.form.title') }}</h2>
                    <p>{{ __('contact.form.subtitle') }}</p>
                </div>

                <div class="contact-message-grid">
                    <div class="contact-message-field">
                        <label for="contact-full-name">{{ __('contact.form.fields.full_name') }} <span aria-hidden="true">*</span></label>
                        <input id="contact-full-name" name="full_name" type="text" placeholder="{{ __('contact.form.placeholders.full_name') }}" required />
                    </div>

                    <div class="contact-message-field">
                        <label for="contact-phone">{{ __('contact.form.fields.phone') }} <span aria-hidden="true">*</span></label>
                        <input id="contact-phone" name="phone" type="tel" placeholder="{{ __('contact.form.placeholders.phone') }}" required />
                    </div>

                    <div class="contact-message-field">
                        <label for="contact-email">{{ __('contact.form.fields.email') }} <span aria-hidden="true">*</span></label>
                        <input id="contact-email" name="email" type="email" placeholder="{{ __('contact.form.placeholders.email') }}" required />
                    </div>

                    <div class="contact-message-field">
                        <label for="contact-preferred-center">{{ __('contact.form.fields.center') }} <span aria-hidden="true">*</span></label>
                        <select id="contact-preferred-center" name="preferred_center" required>
                            <option value="">{{ __('contact.form.placeholders.center') }}</option>
                            @foreach ($operationalCenters as $center)
                                <option value="{{ $center['slug'] }}">{{ $center['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="contact-message-field contact-message-field--full">
                        <label for="contact-reason">{{ __('contact.form.fields.reason') }} <span aria-hidden="true">*</span></label>
                        <select id="contact-reason" name="reason" required>
                            <option value="">{{ __('contact.form.placeholders.reason') }}</option>
                            @foreach (__('contact.form.reasons') as $reason)
                                <option value="{{ \Illuminate\Support\Str::slug($reason) }}">{{ $reason }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="contact-message-field contact-message-field--full">
                        <label for="contact-message-body">{{ __('contact.form.fields.message') }} <span aria-hidden="true">*</span></label>
                        <textarea id="contact-message-body" name="message" rows="4" placeholder="{{ __('contact.form.placeholders.message') }}" required></textarea>
                    </div>
                </div>

                <label class="contact-message-consent" for="contact-consent">
                    <input id="contact-consent" name="consent" type="checkbox" required />
                    <span>{{ __('contact.form.consent') }}</span>
                </label>

                <button type="submit" class="contact-message-submit">
                    <x-lucide-send aria-hidden="true" />
                    <span>{{ __('contact.form.submit') }}</span>
                </button>
            </form>

            <aside class="contact-next-panel" aria-labelledby="contact-next-title">
                <div class="contact-message-heading">
                    <h2 id="contact-next-title">{{ __('contact.next.title') }}</h2>
                    <p>{{ __('contact.next.subtitle') }}</p>
                </div>

                <ol class="contact-next-steps">
                    @foreach (__('contact.next.steps') as $index => $step)
                        <li class="contact-next-step">
                            <span class="contact-next-icon" aria-hidden="true">
                                <x-dynamic-component :component="'lucide-' . $step['icon']" />
                            </span>
                            <span class="contact-next-number">{{ $index + 1 }}</span>
                            <span class="contact-next-copy">
                                <strong>{{ $step['title'] }}</strong>
                                <span>{{ $step['text'] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ol>

                <div class="contact-next-note">
                    <x-lucide-clock aria-hidden="true" />
                    <p>{{ __('contact.next.note') }}</p>
                </div>
            </aside>
        </div>
    </section>

    <section class="contact-faq-section" aria-labelledby="contact-faq-title">
        <div class="contact-faq-panel" x-data="{ open: 0 }">
            <h2 id="contact-faq-title">{{ __('contact.faq.title') }}</h2>

            <div class="contact-faq-list">
                @foreach (__('contact.faq.items') as $index => $item)
                    <article @class(['contact-faq-item', 'contact-faq-item--first' => $loop->first])>
                        <button
                            type="button"
                            class="contact-faq-question"
                            @click="open = open === {{ $index }} ? null : {{ $index }}"
                            :aria-expanded="(open === {{ $index }}).toString()"
                            aria-controls="contact-faq-answer-{{ $index }}"
                        >
                            <span class="contact-faq-icon" aria-hidden="true">?</span>
                            <span class="contact-faq-question-text">{{ $item['question'] }}</span>
                            <span class="contact-faq-chevron" aria-hidden="true">
                                <x-lucide-chevron-down x-show="open !== {{ $index }}" />
                                <x-lucide-chevron-up x-show="open === {{ $index }}" />
                            </span>
                        </button>

                        <div
                            id="contact-faq-answer-{{ $index }}"
                            class="contact-faq-answer"
                            x-show="open === {{ $index }}"
                        >
                            <p>{{ $item['answer'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
