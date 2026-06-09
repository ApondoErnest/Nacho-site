@php
    $locale = app()->getLocale();
    $images = [
        'nacho-yaounde' => 'images/center-nacho-yaounde.png',
        'nacho-nkwen-bamenda' => 'images/center-nacho-nkwen-bamenda.png',
        'nacho-mankon-bamenda' => 'images/center-nacho-nacho-bamenda.png',
        'nacho-douala' => 'images/center-nacho-douala-coming-soon.png',
        'nacho-kumba' => 'images/center-nacho-kumba-coming-soon.png',
    ];
    $centers = collect(config('centers.centers', []));
@endphp

<section {{ $attributes->class(['inspection-centers-showcase']) }}>
    <h2 class="inspection-centers-title">{{ __('home.centers.title') }}</h2>

    <div class="inspection-centers-grid">
        @foreach ($centers as $center)
            @php
                $isOperational = $center['status'] === 'operational';
                $imagePath = $images[$center['slug']] ?? null;
                $imageUrl = $imagePath && file_exists(public_path($imagePath)) ? asset($imagePath) : null;
                $hours = $locale === 'fr' ? ($center['hours_fr'] ?? null) : ($center['hours_en'] ?? null);
                $primaryPhone = $center['phones'][0] ?? null;
                $mapsUrl = $center['maps_url'] ?? route('centers.index');
            @endphp

            <article class="inspection-center-card" id="{{ $center['slug'] }}">
                <h3 class="inspection-center-name">{{ $center['name'] }}</h3>

                @if ($isOperational)
                    <div class="inspection-center-photo">
                        @if ($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $center['name'] }}" loading="eager" />
                        @endif
                    </div>

                    <ul class="inspection-center-details" aria-label="{{ $center['name'] }}">
                        <li>
                            <span class="inspection-center-detail-icon" aria-hidden="true">
                                <x-lucide-map-pin />
                            </span>
                            <span>{{ $center['address'] }}</span>
                        </li>
                    </ul>

                    <a href="{{ $mapsUrl }}" class="inspection-center-map-button" target="_blank" rel="noopener">
                        <span aria-hidden="true">
                            <x-lucide-map-pin class="h-5 w-5" />
                        </span>
                        {{ __('home.centers.maps') }}
                    </a>

                    <ul class="inspection-center-details inspection-center-contact">
                        @if ($primaryPhone)
                            <li>
                                <span class="inspection-center-detail-icon" aria-hidden="true">
                                    <x-lucide-phone />
                                </span>
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $primaryPhone) }}">{{ $primaryPhone }}</a>
                            </li>
                        @endif
                        @if (! empty($center['email']))
                            <li>
                                <span class="inspection-center-detail-icon" aria-hidden="true">
                                    <x-lucide-mail />
                                </span>
                                <a href="mailto:{{ $center['email'] }}">{{ $center['email'] }}</a>
                            </li>
                        @endif
                        @if ($hours)
                            <li>
                                <span class="inspection-center-detail-icon" aria-hidden="true">
                                    <x-lucide-clock />
                                </span>
                                <span>{{ $hours }}</span>
                            </li>
                        @endif
                    </ul>
                @else
                    <div class="inspection-center-coming-visual">
                        @if ($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $center['name'] }}" loading="eager" />
                        @endif
                    </div>

                    <span class="inspection-center-coming-button">{{ __('home.centers.coming_soon') }}</span>
                    <p class="inspection-center-under">{{ __('home.centers.under_construction') }}</p>
                    <span class="inspection-center-divider" aria-hidden="true"></span>
                    <p class="inspection-center-soon">{{ __('home.centers.serving_soon') }}</p>
                @endif
            </article>
        @endforeach
    </div>
</section>
