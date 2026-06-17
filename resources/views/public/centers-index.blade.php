@extends('layouts.public')

@section('title', __('navigation.centers'))

@section('content')
    @php
        $locale = app()->getLocale();
        $centers = collect(config('centers.centers', []));
        $operationalCenterCount = $centers->where('status', 'operational')->count();
        $expansionCenterCount = $centers->where('status', 'under_construction')->count();
        $totalLocations = $centers->count();
        $heroStats = [
            [
                'icon' => 'building-2',
                'value' => $operationalCenterCount,
                'label' => __('components.centers_hero.stats.current_centers'),
            ],
            [
                'icon' => 'construction',
                'value' => $expansionCenterCount,
                'label' => __('components.centers_hero.stats.expansion_centers'),
            ],
            [
                'icon' => 'map-pin',
                'value' => $totalLocations,
                'label' => __('components.centers_hero.stats.locations'),
            ],
            [
                'icon' => 'shield-check',
                'value' => __('components.centers_hero.stats.experience_value'),
                'label' => __('components.centers_hero.stats.experience_label'),
            ],
        ];
        $serviceOptions = [
            ['slug' => 'periodic-technical-inspection', 'label' => __('components.centers_locator.services.periodic'), 'short_label' => __('components.centers_locator.services_short.periodic')],
            ['slug' => 'light-vehicle-inspection', 'label' => __('components.centers_locator.services.light'), 'short_label' => __('components.centers_locator.services_short.light')],
            ['slug' => 'heavy-vehicle-inspection', 'label' => __('components.centers_locator.services.heavy'), 'short_label' => __('components.centers_locator.services_short.heavy')],
            ['slug' => 'counter-visit-re-inspection', 'label' => __('components.centers_locator.services.counter_visit'), 'short_label' => __('components.centers_locator.services_short.counter_visit')],
            ['slug' => 'pre-purchase-inspection', 'label' => __('components.centers_locator.services.pre_purchase'), 'short_label' => __('components.centers_locator.services_short.pre_purchase')],
        ];
        $serviceCatalog = collect($serviceOptions)->keyBy('slug');
        $regionOptions = ['Centre', 'Northwest', 'Littoral', 'Southwest'];
        $centerImageMap = [
            'nacho-yaounde' => 'images/center-nacho-yaounde.png',
            'nacho-nkwen-bamenda' => 'images/center-nacho-nkwen-bamenda.png',
            'nacho-mankon-bamenda' => 'images/center-nacho-nacho-bamenda.png',
            'nacho-douala' => 'images/center-nacho-douala-coming-soon.png',
            'nacho-kumba' => 'images/center-nacho-kumba-coming-soon.png',
        ];
        $expansionImageMap = [
            'nacho-douala' => 'images/centers-douala.png',
            'nacho-kumba' => 'images/centers-kumba.png',
        ];
        $approximateCityCoordinates = [
            'nacho-douala' => ['latitude' => 4.0511, 'longitude' => 9.7679],
            'nacho-kumba' => ['latitude' => 4.6363, 'longitude' => 9.4469],
        ];
        $locatorCenters = $centers
            ->map(function (array $center) use ($locale, $serviceCatalog, $centerImageMap, $approximateCityCoordinates) {
                $suffix = $locale === 'fr'
                    ? ($center['name_suffix_fr'] ?? '')
                    : ($center['name_suffix_en'] ?? '');
                $displayName = trim($center['name'] . ' ' . $suffix);
                $hours = $locale === 'fr' ? ($center['hours_fr'] ?? null) : ($center['hours_en'] ?? null);
                $hoursLines = $locale === 'fr' ? ($center['hours_lines_fr'] ?? []) : ($center['hours_lines_en'] ?? []);
                $imagePath = $centerImageMap[$center['slug']] ?? null;
                $latitude = $center['latitude'] ?? ($approximateCityCoordinates[$center['slug']]['latitude'] ?? null);
                $longitude = $center['longitude'] ?? ($approximateCityCoordinates[$center['slug']]['longitude'] ?? null);
                $isOperational = $center['status'] === 'operational';
                $addressLine = $center['address'] ?: trim($center['city'] . ', ' . $center['region']);
                $mapsUrl = $center['maps_url']
                    ?: 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode(trim($center['name'] . ' ' . $center['city'] . ' Cameroon'));
                $assignedServices = collect($center['services'] ?? [])
                    ->map(fn (string $slug) => $serviceCatalog->get($slug))
                    ->filter()
                    ->values();
                $phones = collect($center['phones'] ?? [])
                    ->map(fn (string $phone) => [
                        'label' => $phone,
                        'href' => 'tel:' . preg_replace('/[^\d+]/', '', $phone),
                    ])
                    ->values();
                $primaryPhone = $phones->first();
                $searchTerms = [
                    $center['slug'],
                    $center['name'],
                    $displayName,
                    $center['city'],
                    $center['region'],
                    $center['address'] ?? null,
                    $center['landmark'] ?? null,
                    ($center['is_headquarters'] ?? false) ? 'Headquarters' : null,
                    $suffix,
                ];

                return [
                    'slug' => $center['slug'],
                    'name' => $displayName,
                    'base_name' => $center['name'],
                    'city' => $center['city'],
                    'region' => $center['region'],
                    'status' => $center['status'],
                    'status_label' => $isOperational ? __('components.center.operational') : __('components.center.under_construction'),
                    'status_note' => $isOperational ? null : __('components.center.opening_notice'),
                    'address' => $center['address'],
                    'address_line' => $addressLine,
                    'landmark' => $center['landmark'] ?? null,
                    'phone_line' => $primaryPhone['label'] ?? null,
                    'phone_primary' => $primaryPhone,
                    'phone_entries' => $phones,
                    'phone_alternatives' => $phones->slice(1)->values(),
                    'email' => $center['email'] ?? null,
                    'email_href' => ! empty($center['email']) ? 'mailto:' . $center['email'] : null,
                    'hours' => $hours,
                    'hours_lines' => $hoursLines,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'is_approximate' => empty($center['latitude']) || empty($center['longitude']),
                    'is_operational' => $isOperational,
                    'is_headquarters' => (bool) ($center['is_headquarters'] ?? false),
                    'postal_address' => $center['postal_address'] ?? null,
                    'institutional_label' => $locale === 'fr'
                        ? ($center['institutional_label_fr'] ?? null)
                        : ($center['institutional_label_en'] ?? null),
                    'support_note' => $locale === 'fr'
                        ? ($center['support_note_fr'] ?? null)
                        : ($center['support_note_en'] ?? null),
                    'services' => $assignedServices->pluck('slug')->all(),
                    'service_labels' => $assignedServices->pluck('label')->all(),
                    'service_short_labels' => $assignedServices->pluck('short_label')->all(),
                    'maps_url' => $mapsUrl,
                    'book_url' => route('book-inspection', ['center' => $center['booking_key'] ?? $center['slug']]),
                    'image_url' => $imagePath && file_exists(public_path($imagePath)) ? asset($imagePath) : null,
                    'search_index' => \Illuminate\Support\Str::lower(implode(' ', array_filter($searchTerms))),
                ];
            })
            ->values();
        $expansionCenterCards = $centers
            ->where('status', 'under_construction')
            ->map(function (array $center) use ($locale, $expansionImageMap) {
                $imagePath = $expansionImageMap[$center['slug']] ?? null;

                return [
                    'slug' => $center['slug'],
                    'name' => $center['name'],
                    'region' => __('components.centers_expansion.region_name', ['region' => $center['region']]),
                    'status_label' => __('components.center.under_construction'),
                    'phase' => $locale === 'fr'
                        ? ($center['expansion_phase_fr'] ?? null)
                        : ($center['expansion_phase_en'] ?? null),
                    'target' => $locale === 'fr'
                        ? ($center['expansion_target_fr'] ?? null)
                        : ($center['expansion_target_en'] ?? null),
                    'last_updated' => $locale === 'fr'
                        ? ($center['expansion_last_updated_fr'] ?? null)
                        : ($center['expansion_last_updated_en'] ?? null),
                    'details_url' => '#expansion-' . $center['slug'],
                    'image_url' => $imagePath && file_exists(public_path($imagePath)) ? asset($imagePath) : null,
                ];
            })
            ->values();
        $locatorLabels = [
            'all_regions' => __('components.centers_locator.all_regions'),
            'all_services' => __('components.centers_locator.all_services'),
            'result_count' => __('components.centers_locator.result_count'),
            'map_loading' => __('components.centers_locator.map_loading'),
            'map_unavailable' => __('components.centers_locator.map_unavailable'),
            'locating' => __('components.centers_locator.locating'),
            'location_denied' => __('components.centers_locator.location_denied'),
            'location_unavailable' => __('components.centers_locator.location_unavailable'),
            'nearest_found' => __('components.centers_locator.nearest_found'),
            'directions' => __('components.center.directions'),
            'book' => __('components.center.book_at_center'),
            'call_center' => __('components.centers_locator.call_center'),
            'send_email' => __('components.centers_locator.send_email'),
        ];
    @endphp

    <section class="centers-page-hero" aria-labelledby="centers-hero-title">
        <img
            src="{{ asset('images/hero-centers.png') }}"
            alt=""
            class="centers-page-hero-image"
            loading="eager"
            fetchpriority="high"
        />
        <div class="centers-page-hero-overlay" aria-hidden="true"></div>

        <div class="centers-page-hero-inner">
            <div class="centers-page-hero-copy">
                <p class="centers-page-hero-eyebrow">
                    {{ __('components.centers_hero.eyebrow') }}
                </p>

                <h1 id="centers-hero-title" class="centers-page-hero-title">
                    {{ __('components.centers_hero.title') }}
                </h1>

                <p class="centers-page-hero-text">
                    {{ __('components.centers_hero.subtitle') }}
                </p>
            </div>

            <div class="centers-page-hero-stats" aria-label="{{ __('components.centers_hero.stats_label') }}">
                @foreach ($heroStats as $stat)
                    <article class="centers-page-hero-stat">
                        <span class="centers-page-hero-stat-icon" aria-hidden="true">
                            <x-dynamic-component :component="'lucide-' . $stat['icon']" />
                        </span>

                        <span class="centers-page-hero-stat-copy">
                            <span class="centers-page-hero-stat-value">{{ $stat['value'] }}</span>
                            <span class="centers-page-hero-stat-label">{{ $stat['label'] }}</span>
                        </span>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section
        class="centers-locator-section"
        aria-labelledby="centers-locator-title"
        x-data="centersLocator({
            centers: @js($locatorCenters),
            labels: @js($locatorLabels),
            desktopDefaultView: 'map'
        })"
        x-init="init()"
    >
        <div class="centers-locator-inner">
            <h2 id="centers-locator-title" class="sr-only">{{ __('components.centers_locator.title') }}</h2>

            <div class="centers-locator-panel">
                <div class="centers-locator-controls">
                    <label class="centers-locator-search" for="centers-search">
                        <x-lucide-search aria-hidden="true" />
                        <input
                            id="centers-search"
                            type="search"
                            x-model.debounce.150ms="query"
                            placeholder="{{ __('components.centers_locator.search_placeholder') }}"
                            autocomplete="off"
                        />
                    </label>

                    <label class="centers-locator-select" for="centers-region">
                        <span class="sr-only">{{ __('components.centers_locator.region_label') }}</span>
                        <select id="centers-region" x-model="region">
                            <option value="all">{{ __('components.centers_locator.all_regions') }}</option>
                            @foreach ($regionOptions as $region)
                                <option value="{{ $region }}">{{ $region }}</option>
                            @endforeach
                        </select>
                        <x-lucide-chevron-down aria-hidden="true" />
                    </label>

                    <label class="centers-locator-select" for="centers-service">
                        <span class="sr-only">{{ __('components.centers_locator.service_label') }}</span>
                        <select id="centers-service" x-model="service">
                            <option value="all">{{ __('components.centers_locator.all_services') }}</option>
                            @foreach ($serviceOptions as $service)
                                <option value="{{ $service['slug'] }}">{{ $service['label'] }}</option>
                            @endforeach
                        </select>
                        <x-lucide-chevron-down aria-hidden="true" />
                    </label>

                    <button type="button" class="centers-locator-reset" @click="resetFilters()">
                        <x-lucide-rotate-ccw aria-hidden="true" />
                        <span>{{ __('components.centers_locator.reset') }}</span>
                    </button>

                    <button type="button" class="centers-locator-nearest" @click="findNearest()" :disabled="locationLoading">
                        <x-lucide-locate-fixed aria-hidden="true" />
                        <span x-text="locationLoading ? '{{ __('components.centers_locator.locating_short') }}' : '{{ __('components.centers_locator.nearest') }}'"></span>
                    </button>
                </div>

                <div class="centers-locator-view-row">
                    <span class="centers-locator-view-label">{{ __('components.centers_locator.view_label') }}</span>

                    <div class="centers-locator-toggle" role="group" aria-label="{{ __('components.centers_locator.view_label') }}">
                        <button
                            type="button"
                            class="centers-locator-toggle-button"
                            :class="{ 'is-active': viewMode === 'list' }"
                            :aria-pressed="(viewMode === 'list').toString()"
                            @click="setViewMode('list')"
                        >
                            <x-lucide-list aria-hidden="true" />
                            <span>{{ __('components.centers_locator.list_view') }}</span>
                        </button>

                        <button
                            type="button"
                            class="centers-locator-toggle-button"
                            :class="{ 'is-active': viewMode === 'map' }"
                            :aria-pressed="(viewMode === 'map').toString()"
                            @click="setViewMode('map')"
                        >
                            <x-lucide-map aria-hidden="true" />
                            <span>{{ __('components.centers_locator.map_view') }}</span>
                        </button>
                    </div>

                    <p class="centers-locator-hint" x-show="viewMode === 'list'">
                        <x-lucide-info aria-hidden="true" />
                        <span>{{ __('components.centers_locator.list_hint') }}</span>
                    </p>
                </div>

                <p class="centers-locator-message" x-show="statusMessage" x-text="statusMessage" x-cloak></p>
            </div>

            <div class="centers-locator-summary">
                <p x-text="resultCountLabel"></p>
            </div>

            <div class="centers-finder-layout">
                <aside class="centers-current-panel" aria-labelledby="centers-current-title">
                    <h3 id="centers-current-title" class="centers-current-title">
                        <span>{{ __('components.centers_locator.current_centers') }}</span>
                        <span x-text="`(${currentCenters.length})`"></span>
                    </h3>

                    <div class="centers-current-list" x-show="currentCenters.length > 0">
                        <template x-for="center in currentCenters" :key="center.slug">
                            <article
                                class="centers-current-card"
                                :class="{ 'is-selected': center.slug === selectedSlug }"
                                :id="`center-card-${center.slug}`"
                                @click="selectCenter(center.slug)"
                            >
                                <div class="centers-current-card-main">
                                    <div class="centers-current-photo">
                                        <template x-if="center.image_url">
                                            <img :src="center.image_url" :alt="center.name" loading="lazy" />
                                        </template>
                                        <template x-if="! center.image_url">
                                            <span aria-hidden="true"><x-lucide-building-2 /></span>
                                        </template>
                                    </div>

                                    <div class="centers-current-summary">
                                        <div class="centers-current-heading">
                                            <div>
                                                <h4 x-text="center.name"></h4>
                                                <p x-show="center.institutional_label" x-text="center.institutional_label"></p>
                                            </div>

                                            <span
                                                class="centers-current-selected"
                                                :class="{ 'is-visible': center.slug === selectedSlug }"
                                                aria-hidden="true"
                                            >
                                                <x-lucide-check />
                                            </span>
                                        </div>

                                        <ul class="centers-current-facts">
                                            <li>
                                                <x-lucide-map-pin aria-hidden="true" />
                                                <span x-text="center.address_line"></span>
                                            </li>
                                            <li x-show="center.hours_lines.length > 0">
                                                <x-lucide-clock aria-hidden="true" />
                                                <span>
                                                    <template x-for="line in center.hours_lines" :key="line">
                                                        <span class="centers-current-hour-line" x-text="line"></span>
                                                    </template>
                                                </span>
                                            </li>
                                            <li x-show="center.phone_line">
                                                <x-lucide-phone aria-hidden="true" />
                                                <span x-text="center.phone_line"></span>
                                            </li>
                                        </ul>

                                        <div class="centers-current-labels" x-show="center.is_headquarters">
                                            <span>{{ __('components.centers_locator.headquarters_badge') }}</span>
                                            <span>{{ __('components.centers_locator.inspection_center_badge') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="centers-current-services" x-show="center.service_short_labels.length > 0">
                                    <template x-for="serviceLabel in center.service_short_labels.slice(0, 3)" :key="serviceLabel">
                                        <span x-text="serviceLabel"></span>
                                    </template>
                                    <span x-show="center.service_short_labels.length > 3" x-text="`+${center.service_short_labels.length - 3}`"></span>
                                </div>
                            </article>
                        </template>
                    </div>

                    <p class="centers-empty-state" x-show="currentCenters.length === 0" x-cloak>
                        {{ __('components.centers_locator.no_results') }}
                    </p>

                    <div class="centers-help-strip">
                        <x-lucide-headphones aria-hidden="true" />
                        <span>
                            <strong>{{ __('components.centers_locator.help_title') }}</strong>
                            <small>{{ __('components.centers_locator.help_text') }}</small>
                        </span>
                        <a href="{{ route('contact') }}">
                            <span>{{ __('components.centers_locator.contact_nacho') }}</span>
                            <x-lucide-arrow-right aria-hidden="true" />
                        </a>
                    </div>
                </aside>

                <section class="centers-focus-panel" aria-label="{{ __('components.centers_locator.selected_profile') }}">
                    <div class="centers-map-panel" x-show="viewMode === 'map'" x-cloak aria-label="{{ __('components.centers_locator.map_label') }}">
                        <div
                            x-ref="map"
                            id="centers-locator-map"
                            class="centers-leaflet-map"
                            role="application"
                            aria-label="{{ __('components.centers_locator.map_label') }}"
                        ></div>

                        <p class="centers-map-loading" x-show="mapLoading">
                            {{ __('components.centers_locator.map_loading') }}
                        </p>
                    </div>

                    <template x-if="selectedCenter">
                        <article class="centers-profile-card">
                            <header class="centers-profile-header">
                                <h3 x-text="selectedCenter.name"></h3>
                                <span>{{ __('components.centers_locator.selected_center') }}</span>
                            </header>

                            <div class="centers-profile-body">
                                <div class="centers-profile-photo">
                                    <template x-if="selectedCenter.image_url">
                                        <img :src="selectedCenter.image_url" :alt="selectedCenter.name" loading="lazy" />
                                    </template>
                                </div>

                                <ul class="centers-profile-facts">
                                    <li>
                                        <x-lucide-map-pin aria-hidden="true" />
                                        <span x-text="selectedCenter.address_line"></span>
                                    </li>
                                    <li x-show="selectedCenter.hours_lines.length > 0">
                                        <x-lucide-clock aria-hidden="true" />
                                        <span>
                                            <template x-for="line in selectedCenter.hours_lines" :key="line">
                                                <span class="centers-current-hour-line" x-text="line"></span>
                                            </template>
                                        </span>
                                    </li>
                                    <li x-show="selectedCenter.phone_line">
                                        <x-lucide-phone aria-hidden="true" />
                                        <a :href="selectedCenter.phone_primary ? selectedCenter.phone_primary.href : '#'" x-text="selectedCenter.phone_line"></a>
                                    </li>
                                    <li x-show="selectedCenter.email">
                                        <x-lucide-mail aria-hidden="true" />
                                        <a :href="selectedCenter.email_href" x-text="selectedCenter.email"></a>
                                    </li>
                                </ul>
                            </div>

                            <div class="centers-profile-services" x-show="selectedCenter.service_labels.length > 0">
                                <h4>{{ __('components.centers_locator.available_services') }}</h4>
                                <div>
                                    <template x-for="serviceLabel in selectedCenter.service_labels" :key="serviceLabel">
                                        <span>
                                            <x-lucide-circle-check aria-hidden="true" />
                                            <span x-text="serviceLabel"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <div class="centers-profile-actions">
                                <a x-show="selectedCenter.is_operational" :href="selectedCenter.book_url" class="centers-profile-action centers-profile-action--primary">
                                    <x-lucide-calendar-days aria-hidden="true" />
                                    <span>{{ __('components.center.book_at_center') }}</span>
                                </a>
                                <a x-show="selectedCenter.phone_primary" :href="selectedCenter.phone_primary ? selectedCenter.phone_primary.href : '#'" class="centers-profile-action">
                                    <x-lucide-phone aria-hidden="true" />
                                    <span>{{ __('components.centers_locator.call_center') }}</span>
                                </a>
                                <a x-show="selectedCenter.email_href" :href="selectedCenter.email_href" class="centers-profile-action">
                                    <x-lucide-mail aria-hidden="true" />
                                    <span>{{ __('components.centers_locator.send_email') }}</span>
                                </a>
                                <a :href="selectedCenter.maps_url" class="centers-profile-action" target="_blank" rel="noopener">
                                    <x-lucide-map-pin aria-hidden="true" />
                                    <span>{{ __('components.centers_locator.view_google_maps') }}</span>
                                </a>
                            </div>
                        </article>
                    </template>
                </section>
            </div>

            <noscript>
                <div class="centers-noscript">
                    <x-public.centers-grid />
                </div>
            </noscript>
        </div>
    </section>

    @if ($expansionCenterCards->isNotEmpty())
        <section class="centers-expansion-section" aria-labelledby="centers-expansion-title">
            <div class="centers-expansion-inner">
                <div class="centers-expansion-panel">
                    <header class="centers-expansion-header">
                        <h2 id="centers-expansion-title">
                            {{ __('components.centers_expansion.title') }}
                            <span>({{ $expansionCenterCards->count() }})</span>
                        </h2>

                        <p>{{ __('components.centers_expansion.summary') }}</p>
                    </header>

                    <div class="centers-expansion-grid">
                        @foreach ($expansionCenterCards as $center)
                            <article id="expansion-{{ $center['slug'] }}" class="centers-expansion-card">
                                <div class="centers-expansion-photo">
                                    @if ($center['image_url'])
                                        <img src="{{ $center['image_url'] }}" alt="{{ $center['name'] }}" loading="lazy" />
                                    @else
                                        <span aria-hidden="true"><x-lucide-construction /></span>
                                    @endif
                                </div>

                                <div class="centers-expansion-body">
                                    <x-lucide-construction class="centers-expansion-watermark" aria-hidden="true" />

                                    <h3>{{ $center['name'] }}</h3>
                                    <p class="centers-expansion-region">{{ $center['region'] }}</p>

                                    <span class="centers-expansion-status">
                                        <x-lucide-construction aria-hidden="true" />
                                        <span>{{ $center['status_label'] }}</span>
                                    </span>

                                    <dl class="centers-expansion-meta">
                                        <div>
                                            <dt>{{ __('components.centers_expansion.current_phase') }}</dt>
                                            <dd class="is-accent">{{ $center['phase'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>{{ __('components.centers_expansion.target') }}</dt>
                                            <dd>{{ $center['target'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>{{ __('components.centers_expansion.last_updated') }}</dt>
                                            <dd>{{ $center['last_updated'] }}</dd>
                                        </div>
                                    </dl>

                                    <a href="{{ $center['details_url'] }}" class="centers-expansion-link">
                                        <span>{{ __('components.centers_expansion.details') }}</span>
                                        <x-lucide-arrow-right aria-hidden="true" />
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
