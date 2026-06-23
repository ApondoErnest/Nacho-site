@extends('layouts.public')

@section('title', __('careers.meta_title'))

@section('content')
    @php
        $locale = app()->getLocale();
        $generalApplicationEmail = config('centers.headquarters.email');
        $generalApplicationMailto = 'mailto:' . $generalApplicationEmail . '?' . http_build_query([
            'subject' => __('careers.mailto.subject'),
            'body' => __('careers.mailto.body'),
        ], '', '&', PHP_QUERY_RFC3986);
        $finderLabels = __('careers.finder');
        $statusLabels = $finderLabels['status'];
        $hiddenStatuses = ['archived'];
        $openStatuses = ['published', 'closing_soon'];
        $visibleVacancies = collect(__('careers.vacancies.items'))
            ->reject(fn (array $vacancy) => in_array($vacancy['status'], $hiddenStatuses, true))
            ->map(function (array $vacancy) use ($generalApplicationEmail, $openStatuses, $statusLabels) {
                $recipient = $vacancy['application_email'] ?: $generalApplicationEmail;
                $subject = __('careers.vacancies.mailto_subject', [
                    'title' => $vacancy['title'],
                    'reference' => $vacancy['reference'],
                ]);
                $body = __('careers.vacancies.mailto_body', [
                    'title' => $vacancy['title'],
                    'reference' => $vacancy['reference'],
                ]);
                $searchParts = array_merge([
                    $vacancy['title'],
                    $vacancy['department'],
                    $vacancy['center'],
                    $vacancy['employment_type'],
                    $vacancy['reference'],
                    $vacancy['summary'],
                    $vacancy['role_purpose'],
                ], $vacancy['responsibilities'], $vacancy['essential'], $vacancy['preferred'], $vacancy['skills'], $vacancy['documents']);

                $vacancy['application_email'] = $recipient;
                $vacancy['status_label'] = $statusLabels[$vacancy['status']] ?? $vacancy['status'];
                $vacancy['deadline_sentence'] = __('careers.finder.deadline_sentence', ['date' => $vacancy['deadline']]);
                $vacancy['card_status_label'] = in_array($vacancy['status'], $openStatuses, true)
                    ? __('careers.finder.card_deadline', ['date' => $vacancy['deadline']])
                    : __('careers.finder.not_currently_open');
                $vacancy['detail_url'] = route('careers.index') . '?vacancy=' . urlencode($vacancy['slug']);
                $vacancy['mailto'] = in_array($vacancy['status'], $openStatuses, true)
                    ? 'mailto:' . $recipient . '?' . http_build_query([
                        'subject' => $subject,
                        'body' => $body,
                    ], '', '&', PHP_QUERY_RFC3986)
                    : null;
                $vacancy['search_index'] = implode(' ', array_filter($searchParts));

                return $vacancy;
            })
            ->values();
        $hasOpenVacancies = $visibleVacancies->contains(fn (array $vacancy) => in_array($vacancy['status'], $openStatuses, true));
        $filterDepartments = $visibleVacancies
            ->map(fn (array $vacancy) => ['key' => $vacancy['department_key'], 'label' => $vacancy['department']])
            ->unique('key')
            ->values();

        $centerFilterLabels = [
            'nacho-yaounde' => 'NACHO Yaounde',
            'nacho-nkwen-bamenda' => 'NACHO Nkwen-Bamenda',
            'nacho-mankon-bamenda' => $locale === 'fr'
                ? 'NACHO Nacho-Bamenda / Siège'
                : 'NACHO Nacho-Bamenda / Headquarters',
            'nacho-douala' => 'NACHO Douala',
            'nacho-kumba' => 'NACHO Kumba',
        ];
        $alwaysShownCenterKeys = ['nacho-yaounde', 'nacho-nkwen-bamenda', 'nacho-mankon-bamenda'];
        $visibleVacancyCenterKeys = $visibleVacancies->pluck('center_key')->unique();
        $filterCenters = collect($centerFilterLabels)
            ->filter(fn (string $label, string $key) => in_array($key, $alwaysShownCenterKeys, true) || $visibleVacancyCenterKeys->contains($key))
            ->map(fn (string $label, string $key) => ['key' => $key, 'label' => $label])
            ->values();

        $employmentTypeLabels = [
            'full-time' => $locale === 'fr' ? 'Temps plein' : 'Full-time',
            'part-time' => $locale === 'fr' ? 'Temps partiel' : 'Part-time',
            'contract' => $locale === 'fr' ? 'Contrat' : 'Contract',
            'internship' => $locale === 'fr' ? 'Stage' : 'Internship',
            'graduate-trainee-placement' => $locale === 'fr'
                ? 'Placement diplômé ou stagiaire'
                : 'Graduate or Trainee Placement',
        ];
        $usedEmploymentTypeKeys = $visibleVacancies->pluck('employment_type_key')->unique();
        $knownEmploymentTypes = collect($employmentTypeLabels)
            ->filter(fn (string $label, string $key) => $usedEmploymentTypeKeys->contains($key))
            ->map(fn (string $label, string $key) => ['key' => $key, 'label' => $label]);
        $extraEmploymentTypes = $visibleVacancies
            ->reject(fn (array $vacancy) => array_key_exists($vacancy['employment_type_key'], $employmentTypeLabels))
            ->map(fn (array $vacancy) => ['key' => $vacancy['employment_type_key'], 'label' => $vacancy['employment_type']])
            ->unique('key')
            ->values();
        $filterEmploymentTypes = $knownEmploymentTypes->merge($extraEmploymentTypes)->values();
        $requestedVacancy = request('vacancy');
        $initialVacancySlug = $visibleVacancies->contains('slug', $requestedVacancy)
            ? $requestedVacancy
            : ($visibleVacancies->first()['slug'] ?? null);
    @endphp

    <section class="careers-hero" aria-labelledby="careers-hero-title">
        <div class="careers-hero-copy">
            <p class="careers-hero-eyebrow">{{ __('careers.hero.eyebrow') }}</p>

            <h1 id="careers-hero-title" class="careers-hero-title">
                {{ __('careers.hero.title') }}
            </h1>

            <p class="careers-hero-text">
                {{ __('careers.hero.subtitle') }}
            </p>

            <div class="careers-hero-actions" aria-label="{{ __('careers.hero.actions_label') }}">
                <a href="#careers-vacancies" class="careers-hero-action careers-hero-action--primary">
                    <x-lucide-briefcase aria-hidden="true" />
                    <span>{{ __('careers.hero.view_positions') }}</span>
                </a>

                <a href="{{ $generalApplicationMailto }}" class="careers-hero-action careers-hero-action--secondary">
                    <x-lucide-mail aria-hidden="true" />
                    <span>{{ __('careers.hero.general_application') }}</span>
                </a>
            </div>

            <div class="careers-trust-grid" aria-label="{{ __('careers.trust.label') }}">
                @foreach (__('careers.trust.items') as $item)
                    <article class="careers-trust-item">
                        <span class="careers-trust-icon" aria-hidden="true">
                            <x-dynamic-component :component="'lucide-' . $item['icon']" />
                        </span>
                        <span class="careers-trust-text">{{ $item['text'] }}</span>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="careers-hero-media" aria-hidden="true">
            <img
                src="{{ asset('images/hero-careers.png') }}"
                alt=""
                loading="eager"
                fetchpriority="high"
            />
        </div>
    </section>

    <section class="careers-purpose-section" aria-labelledby="careers-purpose-title">
        <div class="careers-purpose-inner">
            <div class="careers-purpose-heading">
                <p>{{ __('careers.purpose.eyebrow') }}</p>
                <h2 id="careers-purpose-title">{{ __('careers.purpose.title') }}</h2>
            </div>

            <div class="careers-purpose-grid">
                @foreach (__('careers.purpose.cards') as $card)
                    <article class="careers-purpose-card">
                        <span class="careers-purpose-card-icon" aria-hidden="true">
                            <x-dynamic-component :component="'lucide-' . $card['icon']" />
                        </span>
                        <span class="careers-purpose-card-copy">
                            <span class="careers-purpose-card-title">{{ $card['title'] }}</span>
                            <span class="careers-purpose-card-text">{{ $card['text'] }}</span>
                        </span>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section
        id="careers-vacancies"
        class="careers-vacancies-section"
        aria-labelledby="careers-vacancies-title"
        x-data="careersVacancies({
            vacancies: @js($visibleVacancies),
            hasOpenVacancies: @js($hasOpenVacancies),
            labels: @js($finderLabels),
            initialSlug: @js($initialVacancySlug),
        })"
    >
        <div class="careers-vacancies-inner">
            <div class="careers-opportunities-heading">
                <h2 id="careers-vacancies-title">{{ __('careers.opportunities.title') }}</h2>
                <p>{{ __('careers.opportunities.subtitle') }}</p>
            </div>

            <div class="careers-opportunities-grid">
                @foreach (__('careers.opportunities.areas') as $area)
                    <article class="careers-opportunity-card">
                        <div class="careers-opportunity-card-heading">
                            <span class="careers-opportunity-card-icon" aria-hidden="true">
                                <x-dynamic-component :component="'lucide-' . $area['icon']" />
                            </span>
                            <h3>{{ $area['title'] }}</h3>
                        </div>

                        <ul class="careers-opportunity-role-list">
                            @foreach ($area['roles'] as $role)
                                <li>{{ $role }}</li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>

            <p class="careers-opportunities-note">{{ __('careers.opportunities.path_note') }}</p>

            <div x-show="hasVacancies">
                <div class="careers-vacancy-filters" aria-label="{{ __('careers.finder.filters_label') }}">
                    <label class="careers-vacancy-search" for="career-vacancy-search">
                        <x-lucide-search aria-hidden="true" />
                        <input
                            id="career-vacancy-search"
                            type="search"
                            x-model.debounce.150ms="query"
                            placeholder="{{ __('careers.finder.search_placeholder') }}"
                            autocomplete="off"
                        />
                    </label>

                    <label class="careers-vacancy-select" for="career-vacancy-department">
                        <span>{{ __('careers.finder.department_label') }}</span>
                        <select id="career-vacancy-department" x-model="department">
                            <option value="all">{{ __('careers.finder.all_departments') }}</option>
                            @foreach ($filterDepartments as $option)
                                <option value="{{ $option['key'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        <x-lucide-chevron-down aria-hidden="true" />
                    </label>

                    <label class="careers-vacancy-select" for="career-vacancy-center">
                        <span>{{ __('careers.finder.center_label') }}</span>
                        <select id="career-vacancy-center" x-model="center">
                            <option value="all">{{ __('careers.finder.all_centers') }}</option>
                            @foreach ($filterCenters as $option)
                                <option value="{{ $option['key'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        <x-lucide-chevron-down aria-hidden="true" />
                    </label>

                    <label class="careers-vacancy-select" for="career-vacancy-employment-type">
                        <span>{{ __('careers.finder.employment_type_label') }}</span>
                        <select id="career-vacancy-employment-type" x-model="employmentType">
                            <option value="all">{{ __('careers.finder.all_types') }}</option>
                            @foreach ($filterEmploymentTypes as $option)
                                <option value="{{ $option['key'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        <x-lucide-chevron-down aria-hidden="true" />
                    </label>

                    <button type="button" class="careers-vacancy-reset" @click="resetFilters()">
                        <x-lucide-refresh-cw aria-hidden="true" />
                        <span>{{ __('careers.finder.reset_filters') }}</span>
                    </button>
                </div>

                <div class="careers-vacancy-layout" x-show="hasFilteredVacancies" x-cloak>
                    <div class="careers-vacancy-list-panel">
                        <h3 x-text="hasOpenVacancies ? labels.current_openings : labels.vacancy_profiles"></h3>

                        <div class="careers-vacancy-list">
                            <template x-for="vacancy in filteredVacancies" :key="vacancy.slug">
                                <article
                                    class="careers-vacancy-card"
                                    :class="{ 'is-selected': selectedSlug === vacancy.slug }"
                                    tabindex="0"
                                    :aria-current="selectedSlug === vacancy.slug ? 'true' : 'false'"
                                    @click="selectVacancy(vacancy.slug)"
                                    @keydown.enter.prevent="selectVacancy(vacancy.slug)"
                                    @keydown.space.prevent="selectVacancy(vacancy.slug)"
                                >
                                    <div class="careers-vacancy-card-main">
                                        <span class="careers-vacancy-card-icon" aria-hidden="true">
                                            <template x-if="vacancy.icon === 'monitor'">
                                                <x-lucide-monitor />
                                            </template>
                                            <template x-if="vacancy.icon === 'user'">
                                                <x-lucide-user />
                                            </template>
                                            <template x-if="vacancy.icon !== 'monitor' && vacancy.icon !== 'user'">
                                                <x-lucide-briefcase />
                                            </template>
                                        </span>

                                        <div class="careers-vacancy-card-copy">
                                            <h4 x-text="vacancy.title"></h4>

                                            <dl class="careers-vacancy-card-meta">
                                                <div>
                                                    <dt class="sr-only">{{ __('careers.finder.labels.department') }}</dt>
                                                    <dd>
                                                        <x-lucide-briefcase aria-hidden="true" />
                                                        <span x-text="vacancy.department"></span>
                                                    </dd>
                                                </div>
                                                <div>
                                                    <dt class="sr-only">{{ __('careers.finder.labels.center') }}</dt>
                                                    <dd>
                                                        <x-lucide-map-pin aria-hidden="true" />
                                                        <span x-text="vacancy.center"></span>
                                                    </dd>
                                                </div>
                                                <div>
                                                    <dt class="sr-only">{{ __('careers.finder.labels.employment_type') }}</dt>
                                                    <dd>
                                                        <x-lucide-clock aria-hidden="true" />
                                                        <span x-text="vacancy.employment_type"></span>
                                                    </dd>
                                                </div>
                                                <div>
                                                    <dt class="sr-only">{{ __('careers.finder.labels.deadline') }}</dt>
                                                    <dd>
                                                        <x-lucide-calendar-days aria-hidden="true" />
                                                        <span x-text="vacancy.card_status_label"></span>
                                                    </dd>
                                                </div>
                                            </dl>

                                            <p class="careers-vacancy-card-summary" x-text="vacancy.summary"></p>
                                        </div>
                                    </div>

                                    <button type="button" class="careers-vacancy-view" @click.stop="toggleMobileDetails(vacancy.slug)">
                                        <span x-text="hasOpenVacancies ? labels.view_position : labels.view_profile"></span>
                                        <x-lucide-arrow-right aria-hidden="true" />
                                    </button>

                                    <div class="careers-mobile-details" x-show="mobileOpenSlug === vacancy.slug" x-cloak>
                                        <template x-if="hasOpenVacancies">
                                            <div>
                                                <section class="careers-mobile-detail-block">
                                                    <h5>{{ __('careers.finder.labels.role_purpose') }}</h5>
                                                    <p x-text="vacancy.role_purpose"></p>
                                                </section>
                                                <section class="careers-mobile-detail-block">
                                                    <h5>{{ __('careers.finder.labels.responsibilities') }}</h5>
                                                    <ul>
                                                        <template x-for="item in vacancy.responsibilities" :key="item">
                                                            <li x-text="item"></li>
                                                        </template>
                                                    </ul>
                                                </section>
                                                <section class="careers-mobile-detail-block">
                                                    <h5>{{ __('careers.finder.labels.documents') }}</h5>
                                                    <ul>
                                                        <template x-for="document in vacancy.documents" :key="document">
                                                            <li x-text="document"></li>
                                                        </template>
                                                    </ul>
                                                </section>
                                                <p class="careers-vacancy-deadline" x-text="vacancy.deadline_sentence"></p>
                                                <div class="careers-mobile-actions">
                                                    <template x-if="canApply(vacancy)">
                                                        <a class="careers-vacancy-action careers-vacancy-action--primary" :href="vacancy.mailto">
                                                            <x-lucide-mail aria-hidden="true" />
                                                            <span>{{ __('careers.finder.apply_by_email') }}</span>
                                                        </a>
                                                    </template>
                                                    <template x-if="!canApply(vacancy)">
                                                        <button type="button" class="careers-vacancy-action careers-vacancy-action--disabled" disabled>
                                                            <x-lucide-mail aria-hidden="true" />
                                                            <span>{{ __('careers.finder.apply_by_email') }}</span>
                                                        </button>
                                                    </template>
                                                    <button type="button" class="careers-vacancy-action careers-vacancy-action--secondary" @click="shareVacancy(vacancy)">
                                                        <x-lucide-share-2 aria-hidden="true" />
                                                        <span>{{ __('careers.finder.share_vacancy') }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>

                                        <div class="careers-mobile-empty" x-show="! hasOpenVacancies">
                                            <p class="careers-vacancies-empty-title">{{ __('careers.finder.empty_title') }}</p>
                                            <p>{{ __('careers.finder.empty_text') }}</p>
                                            <div class="careers-empty-actions">
                                                <a href="{{ $generalApplicationMailto }}" class="careers-empty-action">{{ __('careers.hero.general_application') }}</a>
                                                <a href="{{ route('about') }}" class="careers-empty-action careers-empty-action--secondary">{{ __('careers.finder.learn_about') }}</a>
                                                <a href="{{ route('contact') }}" class="careers-empty-action careers-empty-action--secondary">{{ __('careers.finder.contact_hr') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </template>
                        </div>

                        <p class="careers-vacancy-count" x-text="showingCountLabel"></p>
                    </div>

                    <aside class="careers-vacancy-detail-panel" aria-label="{{ __('careers.finder.details_label') }}" x-cloak>
                        <template x-if="hasOpenVacancies && selectedVacancy">
                            <div>
                        <div class="careers-vacancy-detail-header">
                            <div>
                                <h3 x-text="selectedVacancy.title"></h3>
                                <span
                                    class="careers-vacancy-status"
                                    :class="'careers-vacancy-status--' + selectedVacancy.status"
                                    x-text="selectedVacancy.status_label"
                                ></span>
                            </div>
                        </div>

                        <dl class="careers-vacancy-detail-meta">
                            <div>
                                <dt>{{ __('careers.finder.labels.reference') }}</dt>
                                <dd><x-lucide-file-text aria-hidden="true" /><span x-text="selectedVacancy.reference"></span></dd>
                            </div>
                            <div>
                                <dt>{{ __('careers.finder.labels.department') }}</dt>
                                <dd><x-lucide-briefcase aria-hidden="true" /><span x-text="selectedVacancy.department"></span></dd>
                            </div>
                            <div>
                                <dt>{{ __('careers.finder.labels.center') }}</dt>
                                <dd><x-lucide-map-pin aria-hidden="true" /><span x-text="selectedVacancy.center"></span></dd>
                            </div>
                            <div>
                                <dt>{{ __('careers.finder.labels.employment_type') }}</dt>
                                <dd><x-lucide-clock aria-hidden="true" /><span x-text="selectedVacancy.employment_type"></span></dd>
                            </div>
                            <div>
                                <dt>{{ __('careers.finder.labels.deadline') }}</dt>
                                <dd><x-lucide-calendar-days aria-hidden="true" /><span x-text="selectedVacancy.deadline"></span></dd>
                            </div>
                            <div x-show="selectedVacancy.positions">
                                <dt>{{ __('careers.finder.labels.positions') }}</dt>
                                <dd><x-lucide-users aria-hidden="true" /><span x-text="selectedVacancy.positions"></span></dd>
                            </div>
                        </dl>

                        <section class="careers-vacancy-detail-block">
                            <h4>{{ __('careers.finder.labels.role_purpose') }}</h4>
                            <p x-text="selectedVacancy.role_purpose"></p>
                        </section>

                        <div class="careers-vacancy-detail-columns">
                            <section class="careers-vacancy-detail-block">
                                <h4>{{ __('careers.finder.labels.responsibilities') }}</h4>
                                <ul>
                                    <template x-for="item in selectedVacancy.responsibilities" :key="item">
                                        <li x-text="item"></li>
                                    </template>
                                </ul>
                            </section>

                            <section class="careers-vacancy-detail-block">
                                <h4>{{ __('careers.finder.labels.essential') }}</h4>
                                <ul>
                                    <template x-for="item in selectedVacancy.essential" :key="item">
                                        <li x-text="item"></li>
                                    </template>
                                </ul>
                            </section>

                            <section class="careers-vacancy-detail-block">
                                <h4>{{ __('careers.finder.labels.preferred') }}</h4>
                                <ul>
                                    <template x-for="item in selectedVacancy.preferred" :key="item">
                                        <li x-text="item"></li>
                                    </template>
                                </ul>
                            </section>
                        </div>

                        <section class="careers-vacancy-detail-block">
                            <h4>{{ __('careers.finder.labels.skills') }}</h4>
                            <div class="careers-vacancy-skill-list">
                                <template x-for="skill in selectedVacancy.skills" :key="skill">
                                    <span x-text="skill"></span>
                                </template>
                            </div>
                        </section>

                        <section class="careers-vacancy-detail-block">
                            <h4>{{ __('careers.finder.labels.documents') }}</h4>
                            <ul class="careers-vacancy-document-list">
                                <template x-for="document in selectedVacancy.documents" :key="document">
                                    <li>
                                        <x-lucide-file-text aria-hidden="true" />
                                        <span x-text="document"></span>
                                    </li>
                                </template>
                            </ul>
                        </section>

                        <p class="careers-vacancy-deadline" x-text="selectedVacancy.deadline_sentence"></p>
                        <p class="careers-vacancy-status-message" x-show="statusMessageFor(selectedVacancy)" x-text="statusMessageFor(selectedVacancy)"></p>

                        <div class="careers-vacancy-detail-actions">
                            <template x-if="canApply(selectedVacancy)">
                                <a class="careers-vacancy-action careers-vacancy-action--primary" :href="selectedVacancy.mailto">
                                    <x-lucide-mail aria-hidden="true" />
                                    <span>{{ __('careers.finder.apply_by_email') }}</span>
                                </a>
                            </template>
                            <template x-if="!canApply(selectedVacancy)">
                                <button type="button" class="careers-vacancy-action careers-vacancy-action--disabled" disabled>
                                    <x-lucide-mail aria-hidden="true" />
                                    <span>{{ __('careers.finder.apply_by_email') }}</span>
                                </button>
                            </template>
                            <button type="button" class="careers-vacancy-action careers-vacancy-action--secondary" @click="shareVacancy(selectedVacancy)">
                                <x-lucide-share-2 aria-hidden="true" />
                                <span>{{ __('careers.finder.share_vacancy') }}</span>
                            </button>
                            <button type="button" class="careers-vacancy-action careers-vacancy-action--secondary" @click="printVacancy()">
                                <x-lucide-printer aria-hidden="true" />
                                <span>{{ __('careers.finder.print_job') }}</span>
                            </button>
                        </div>

                        <p class="careers-vacancy-application-note">
                            <x-lucide-info aria-hidden="true" />
                            <span>{{ __('careers.finder.application_notice') }}</span>
                        </p>
                        <p class="careers-vacancy-attachment-note">{{ __('careers.finder.attach_notice') }}</p>
                        <p class="careers-vacancy-share-status" x-show="statusMessage" x-text="statusMessage"></p>
                            </div>
                        </template>

                        <div class="careers-vacancy-side-empty" x-show="! hasOpenVacancies">
                            <span class="careers-vacancy-side-empty-icon" aria-hidden="true">
                                <x-lucide-briefcase />
                            </span>
                            <h3>{{ __('careers.finder.empty_title') }}</h3>
                            <p>{{ __('careers.finder.empty_text') }}</p>
                            <div class="careers-empty-actions">
                                <a href="{{ $generalApplicationMailto }}" class="careers-empty-action">{{ __('careers.hero.general_application') }}</a>
                                <a href="{{ route('about') }}" class="careers-empty-action careers-empty-action--secondary">{{ __('careers.finder.learn_about') }}</a>
                                <a href="{{ route('contact') }}" class="careers-empty-action careers-empty-action--secondary">{{ __('careers.finder.contact_hr') }}</a>
                            </div>
                        </div>
                    </aside>
                </div>

                <div class="careers-vacancies-empty" x-show="! hasFilteredVacancies" x-cloak>
                    <p class="careers-vacancies-empty-title">{{ __('careers.finder.no_matches_title') }}</p>
                    <p>{{ __('careers.finder.no_matches_text') }}</p>
                    <button type="button" class="careers-empty-action" @click="resetFilters()">
                        {{ __('careers.finder.reset_filters') }}
                    </button>
                </div>
            </div>

            <div class="careers-vacancies-empty" x-show="! hasVacancies" x-cloak>
                <p class="careers-vacancies-empty-title">{{ __('careers.finder.empty_title') }}</p>
                <p>{{ __('careers.finder.empty_text') }}</p>
                <div class="careers-empty-actions">
                    <a href="{{ $generalApplicationMailto }}" class="careers-empty-action">{{ __('careers.hero.general_application') }}</a>
                    <a href="{{ route('about') }}" class="careers-empty-action careers-empty-action--secondary">{{ __('careers.finder.learn_about') }}</a>
                    <a href="{{ route('contact') }}" class="careers-empty-action careers-empty-action--secondary">{{ __('careers.finder.contact_hr') }}</a>
                </div>
            </div>

            <section class="careers-final-cta" aria-labelledby="careers-final-cta-title">
                <aside class="careers-final-cta-general">
                    <span class="careers-final-cta-general-icon" aria-hidden="true">
                        <x-lucide-inbox />
                    </span>

                    <div class="careers-final-cta-general-copy">
                        <h2 id="careers-final-cta-title">{{ __('careers.finder.general_heading') }}</h2>
                        <p>{{ __('careers.finder.general_text') }}</p>

                        <div class="careers-final-cta-actions">
                            <a href="{{ $generalApplicationMailto }}" class="careers-final-cta-action careers-final-cta-action--primary">
                                <span>{{ __('careers.hero.general_application') }}</span>
                            </a>

                            <a href="{{ route('about') }}" class="careers-final-cta-action careers-final-cta-action--secondary">
                                <span>{{ __('careers.finder.learn_about') }}</span>
                                <x-lucide-arrow-right aria-hidden="true" />
                            </a>
                        </div>

                        <p class="careers-general-note">{{ __('careers.finder.general_note') }}</p>
                    </div>
                </aside>

                <aside class="careers-final-cta-process" aria-labelledby="careers-application-steps-title">
                    <h2 id="careers-application-steps-title">{{ __('careers.finder.application_steps_title') }}</h2>

                    <div class="careers-final-cta-step-grid">
                        @foreach (__('careers.finder.application_steps') as $index => $step)
                            <article class="careers-final-cta-step">
                                <span class="careers-final-cta-step-number">{{ $index + 1 }}</span>
                                <span class="careers-final-cta-step-icon" aria-hidden="true">
                                    <x-dynamic-component :component="'lucide-' . $step['icon']" />
                                </span>
                                <span class="careers-final-cta-step-copy">
                                    <span class="careers-final-cta-step-title">{{ $step['title'] }}</span>
                                    <span class="careers-final-cta-step-text">{{ $step['text'] }}</span>
                                </span>
                            </article>
                        @endforeach
                    </div>

                    <p class="careers-final-cta-safety">
                        <x-lucide-shield-check aria-hidden="true" />
                        <span>{{ __('careers.finder.safety_notice') }}</span>
                    </p>
                </aside>
            </section>
        </div>
    </section>
@endsection
