@extends('layouts.public')

@section('title', __('tariffs.meta.title'))

@section('content')
    @php
        $proofItems = [
            ['icon' => 'list-checks', 'title' => __('tariffs.hero.proof.categories')],
            ['icon' => 'shield-check', 'title' => __('tariffs.hero.proof.published')],
            ['icon' => 'lock-keyhole', 'title' => __('tariffs.hero.proof.no_hidden')],
        ];
        $locale = app()->getLocale();
        $tariffRows = collect(config('home.tariff_preview', []))->values()->map(function (array $row, int $index) use ($locale) {
            $row['key'] = 'tariff-' . ($row['number'] ?? $index + 1);
            $row['translation_key'] = 'row_' . ($row['number'] ?? $index + 1);
            $row['vehicle_type'] = $row["vehicle_type_{$locale}"] ?? $row['vehicle_type_en'];
            $row['validity'] = $row["validity_{$locale}"] ?? $row['validity_en'];
            $priceAmount = (int) preg_replace('/\D+/', '', $row['price']);
            $row['price_amount'] = $locale === 'fr'
                ? number_format($priceAmount, 0, ',', ' ')
                : number_format($priceAmount);
            $validityParts = preg_split('/\s+/', trim($row['validity']), 2);
            $row['validity_number'] = isset($validityParts[0]) ? ((string) ((int) $validityParts[0])) : $row['validity'];
            $row['validity_unit'] = isset($validityParts[1]) ? ucfirst($validityParts[1]) : '';

            return $row;
        });
        $selectedTariff = $tariffRows->firstWhere('number', 2) ?? $tariffRows->first();
        $tariffIcons = [
            1 => 'car-taxi-front',
            2 => 'car-front',
            3 => 'truck',
            4 => 'bus-front',
            5 => 'bus',
            6 => 'tractor',
            7 => 'construction',
        ];
        $infoIcons = [
            'payment' => 'credit-card',
            'documents' => 'file-text',
            'validity' => 'calendar-days',
            'updates' => 'shield-check',
        ];
    @endphp

    <section class="tariffs-hero" aria-labelledby="tariffs-hero-title">
        <img
            src="{{ asset('images/hero-tariffs.png') }}"
            alt=""
            class="tariffs-hero-image"
            loading="eager"
            fetchpriority="high"
        />
        <div class="tariffs-hero-overlay" aria-hidden="true"></div>

        <div class="tariffs-hero-inner">
            <div class="tariffs-hero-copy">
                <p class="tariffs-hero-eyebrow">{{ __('tariffs.hero.eyebrow') }}</p>

                <h1 id="tariffs-hero-title" class="tariffs-hero-title">
                    {{ __('tariffs.hero.title') }}
                </h1>

                <p class="tariffs-hero-text">
                    {{ __('tariffs.hero.subtitle') }}
                </p>

                <div class="tariffs-hero-proof" aria-label="{{ __('tariffs.hero.proof_label') }}">
                    @foreach ($proofItems as $item)
                        <div class="tariffs-hero-proof-item">
                            <span class="tariffs-hero-proof-icon" aria-hidden="true">
                                <x-dynamic-component :component="'lucide-' . $item['icon']" />
                            </span>
                            <span>{{ $item['title'] }}</span>
                        </div>
                    @endforeach
                </div>

                <article class="tariffs-hero-notice">
                    <span class="tariffs-hero-notice-icon" aria-hidden="true">
                        <x-lucide-info />
                    </span>

                    <div class="tariffs-hero-notice-copy">
                        <h2>{{ __('tariffs.hero.notice.title') }}</h2>
                        <p>{{ __('tariffs.hero.notice.text') }}</p>
                    </div>
                </article>

                <div class="tariffs-hero-meta">
                    <p>
                        <strong>{{ __('tariffs.hero.meta.last_verified_label') }}</strong>
                        <span>{{ __('tariffs.hero.meta.last_verified') }}</span>
                    </p>
                    <span class="tariffs-hero-meta-dot" aria-hidden="true"></span>
                    <p>
                        <strong>{{ __('tariffs.hero.meta.effective_label') }}</strong>
                        <span>{{ __('tariffs.hero.meta.effective') }}</span>
                    </p>
                    <a href="#tariff-notice" class="tariffs-hero-notice-link">
                        <span>{{ __('tariffs.hero.meta.notice_link') }}</span>
                        <x-lucide-arrow-right aria-hidden="true" />
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section
        id="tariff-finder"
        class="tariffs-console-section"
        aria-labelledby="tariffs-console-title"
        x-data="{ selectedTariff: '{{ $selectedTariff['key'] ?? 'tariff-2' }}' }"
    >
        <div class="tariffs-console-grid">
            <div class="tariffs-console-picker">
                <p class="tariffs-console-step">{{ __('tariffs.console.step') }}</p>
                <h2 id="tariffs-console-title">{{ __('tariffs.console.title') }}</h2>
                <p class="tariffs-console-intro">{{ __('tariffs.console.subtitle') }}</p>

                <div class="tariffs-console-cards" role="group" aria-label="{{ __('tariffs.console.category_label') }}">
                    @foreach ($tariffRows as $row)
                        @php
                            $rowTranslation = 'tariffs.console.rows.' . $row['translation_key'];
                            $icon = $tariffIcons[$row['number']] ?? 'car';
                        @endphp

                        <button
                            type="button"
                            data-tariff-card="{{ $row['key'] }}"
                            class="tariffs-console-category-card"
                            @click="selectedTariff = '{{ $row['key'] }}'"
                            :class="{ 'is-selected': selectedTariff === '{{ $row['key'] }}' }"
                            :aria-pressed="(selectedTariff === '{{ $row['key'] }}').toString()"
                        >
                            <span
                                class="tariffs-console-selected-check"
                                x-show="selectedTariff === '{{ $row['key'] }}'"
                                x-transition.opacity.duration.150ms
                                aria-hidden="true"
                            >
                                <x-lucide-check />
                            </span>
                            <span class="tariffs-console-card-icon" aria-hidden="true">
                                <x-dynamic-component :component="'lucide-' . $icon" />
                            </span>
                            <span class="tariffs-console-card-code">{{ __($rowTranslation . '.code') }}</span>
                            <span class="tariffs-console-card-title">{{ __($rowTranslation . '.card_title') }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="tariffs-console-help">
                    <span class="tariffs-console-help-icon" aria-hidden="true">
                        <x-lucide-circle-help />
                    </span>
                    <p>{{ __('tariffs.console.help.text') }}</p>
                    <a href="{{ route('contact') }}">
                        <span>{{ __('tariffs.console.help.link') }}</span>
                        <x-lucide-arrow-right aria-hidden="true" />
                    </a>
                </div>
            </div>

            <aside class="tariffs-console-result-card" aria-live="polite">
                <div class="tariffs-console-mobile-control">
                    <label for="tariffs-mobile-category">{{ __('tariffs.console.mobile_select_label') }}</label>
                    <select id="tariffs-mobile-category" x-model="selectedTariff">
                        @foreach ($tariffRows as $row)
                            @php
                                $rowTranslation = 'tariffs.console.rows.' . $row['translation_key'];
                            @endphp
                            <option value="{{ $row['key'] }}">
                                {{ __($rowTranslation . '.code') }} - {{ __($rowTranslation . '.panel_title') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @foreach ($tariffRows as $row)
                    @php
                        $rowTranslation = 'tariffs.console.rows.' . $row['translation_key'];
                        $isSelected = ($selectedTariff['key'] ?? null) === $row['key'];
                        $icon = $tariffIcons[$row['number']] ?? 'car';
                    @endphp

                    <article
                        class="tariffs-console-result"
                        x-show="selectedTariff === '{{ $row['key'] }}'"
                        x-transition.opacity.duration.150ms
                        @if (! $isSelected) x-cloak @endif
                    >
                        <p class="tariffs-console-result-eyebrow">{{ __('tariffs.console.selected_label') }}</p>

                        <div class="tariffs-console-result-header">
                            <span class="tariffs-console-result-icon" aria-hidden="true">
                                <x-dynamic-component :component="'lucide-' . $icon" />
                            </span>
                            <span class="tariffs-console-result-code">{{ __($rowTranslation . '.code') }}</span>
                            <div class="tariffs-console-result-heading">
                                <h3>{{ __($rowTranslation . '.panel_title') }}</h3>
                                <p>{{ __($rowTranslation . '.description') }}</p>
                            </div>
                        </div>

                        <div class="tariffs-console-result-stats">
                            <div class="tariffs-console-result-stat">
                                <p>{{ __('tariffs.console.fee_label') }}</p>
                                <strong>
                                    <span>{{ $row['price_amount'] }}</span>
                                    <small>FCFA</small>
                                </strong>
                            </div>

                            <div class="tariffs-console-result-stat tariffs-console-result-stat--validity">
                                <p>{{ __('tariffs.console.validity_label') }}</p>
                                <strong>
                                    <x-lucide-calendar-days aria-hidden="true" />
                                    <span>{{ $row['validity_number'] }}</span>
                                    <small>{{ $row['validity_unit'] }}</small>
                                </strong>
                            </div>
                        </div>

                        <div class="tariffs-console-result-actions">
                            <a href="{{ route('book-inspection') }}?category={{ urlencode($row['filter_id']) }}" class="tariffs-console-book">
                                <x-lucide-calendar-check aria-hidden="true" />
                                <span>{{ __('tariffs.console.book_category') }}</span>
                                <x-lucide-arrow-right aria-hidden="true" />
                            </a>

                            <a href="#tariff-documents" class="tariffs-console-documents">
                                <x-lucide-file-text aria-hidden="true" />
                                <span>{{ __('tariffs.console.documents') }}</span>
                            </a>
                        </div>

                        <a href="#tariff-schedule" class="tariffs-console-show-all">
                            <x-lucide-refresh-cw aria-hidden="true" />
                            <span>{{ __('tariffs.console.show_all') }}</span>
                            <x-lucide-arrow-right aria-hidden="true" />
                        </a>
                    </article>
                @endforeach
            </aside>
        </div>
    </section>

    <section id="tariff-documents" class="tariffs-details-section" aria-labelledby="tariffs-coverage-title">
        <div class="tariffs-details-grid">
            <article class="tariffs-coverage-panel">
                <p class="tariffs-details-step">{{ __('tariffs.details.coverage.step') }}</p>

                <div class="tariffs-coverage-grid">
                    @foreach (__('tariffs.details.coverage.items') as $item)
                        <div class="tariffs-coverage-item">
                            <span class="tariffs-coverage-icon" aria-hidden="true">
                                <x-lucide-circle-check />
                            </span>
                            <div>
                                <h2 @if ($loop->first) id="tariffs-coverage-title" @endif>{{ $item['title'] }}</h2>
                                <p>{{ $item['text'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <aside id="tariff-notice" class="tariffs-note-panel" aria-label="{{ __('tariffs.details.notice.aria_label') }}">
                <div class="tariffs-note-block">
                    <span class="tariffs-note-icon" aria-hidden="true">
                        <x-lucide-info />
                    </span>
                    <div>
                        <h2>{{ __('tariffs.details.notice.important.title') }}</h2>
                        <p>{{ __('tariffs.details.notice.important.text') }}</p>
                    </div>
                </div>

                <div class="tariffs-note-divider" aria-hidden="true"></div>

                <div class="tariffs-note-block">
                    <span class="tariffs-note-icon" aria-hidden="true">
                        <x-lucide-shield-check />
                    </span>
                    <div>
                        <h2>{{ __('tariffs.details.notice.homologation.title') }}</h2>
                        <p>{{ __('tariffs.details.notice.homologation.text') }}</p>
                        <strong>{{ __('tariffs.details.notice.homologation.effective') }}</strong>
                    </div>
                </div>
            </aside>

            <article class="tariffs-info-panel">
                <p class="tariffs-details-step">{{ __('tariffs.details.info.step') }}</p>

                <div class="tariffs-info-grid">
                    @foreach (__('tariffs.details.info.items') as $item)
                        <div class="tariffs-info-item">
                            <span class="tariffs-info-icon" aria-hidden="true">
                                <x-dynamic-component :component="'lucide-' . ($infoIcons[$item['key']] ?? 'info')" />
                            </span>
                            <div>
                                <h2>{{ $item['title'] }}</h2>
                                <p>{{ $item['text'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="tariffs-faq-panel" x-data="{ openFaq: null }">
                <p class="tariffs-details-step">{{ __('tariffs.details.faq.step') }}</p>

                <div class="tariffs-faq-list">
                    @foreach (__('tariffs.details.faq.items') as $item)
                        <div class="tariffs-faq-item">
                            <button
                                type="button"
                                class="tariffs-faq-question"
                                @click="openFaq = openFaq === {{ $loop->index }} ? null : {{ $loop->index }}"
                                :aria-expanded="(openFaq === {{ $loop->index }}).toString()"
                                aria-controls="tariffs-faq-answer-{{ $loop->index }}"
                            >
                                <span class="tariffs-faq-icon" aria-hidden="true">
                                    <x-lucide-circle-help />
                                </span>
                                <span>{{ $item['question'] }}</span>
                                <x-lucide-chevron-down
                                    class="tariffs-faq-chevron"
                                    x-bind:class="{ 'is-open': openFaq === {{ $loop->index }} }"
                                    aria-hidden="true"
                                />
                            </button>
                            <div
                                id="tariffs-faq-answer-{{ $loop->index }}"
                                class="tariffs-faq-answer"
                                x-show="openFaq === {{ $loop->index }}"
                                x-transition.opacity.duration.150ms
                                x-cloak
                            >
                                <p>{{ $item['answer'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>
        </div>
    </section>
@endsection
