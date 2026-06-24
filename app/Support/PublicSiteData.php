<?php

namespace App\Support;

use App\Enums\CareerPostStatus;
use App\Enums\CenterStatus;
use App\Models\CareerPost;
use App\Models\Center;
use App\Models\Page;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Tariff;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PublicSiteData
{
    private ?Collection $centers = null;

    private ?Collection $services = null;

    private ?Collection $tariffs = null;

    private ?array $headquarters = null;

    /**
     * Return center data in the shape the current public Blade views expect.
     */
    public function centers(): Collection
    {
        if ($this->centers instanceof Collection) {
            return $this->centers;
        }

        if (! Schema::hasTable('centers')) {
            return $this->centers = collect(config('centers.centers', []));
        }

        $centers = Center::query()
            ->with([
                'contacts' => fn ($query) => $query->public()->orderBy('display_order'),
                'hours' => fn ($query) => $query->orderByRaw($this->weekdayOrderSql()),
                'services' => fn ($query) => $query->active()->orderBy('display_order'),
            ])
            ->active()
            ->orderBy('display_order')
            ->get();

        if ($centers->isEmpty()) {
            return $this->centers = collect(config('centers.centers', []));
        }

        return $this->centers = $centers->map(fn (Center $center): array => $this->mapCenter($center));
    }

    /**
     * Return headquarters contact data from DB settings/center, falling back to config.
     *
     * @return array<string, mixed>
     */
    public function headquarters(): array
    {
        if (is_array($this->headquarters)) {
            return $this->headquarters;
        }

        $fallback = config('centers.headquarters', []);
        $hqCenter = $this->centers()->firstWhere('is_headquarters', true);
        $phones = collect($hqCenter['phones'] ?? $fallback['phones'] ?? [])->values();
        $primaryPhone = $this->setting('contact_phone') ?? $phones->first() ?? ($fallback['phone_primary'] ?? null);
        $email = $this->setting('contact_email') ?? ($hqCenter['email'] ?? $fallback['email'] ?? null);
        $address = $this->setting('address') ?? ($hqCenter['address'] ?? $fallback['address'] ?? null);

        return $this->headquarters = [
            'label_en' => $fallback['label_en'] ?? 'Main Headquarter',
            'label_fr' => $fallback['label_fr'] ?? 'Siege principal',
            'address' => $address,
            'postal_box' => $this->setting('postal_box') ?? ($hqCenter['postal_address'] ?? $fallback['postal_box'] ?? null),
            'email' => $email,
            'phones' => $phones->isNotEmpty() ? $phones->all() : ($fallback['phones'] ?? []),
            'phone_primary' => $primaryPhone,
            'phone_primary_tel' => $primaryPhone ? $this->phoneHref($primaryPhone) : ($fallback['phone_primary_tel'] ?? null),
        ];
    }

    /**
     * Return service data in the shape the current public Blade views expect.
     */
    public function services(): Collection
    {
        if ($this->services instanceof Collection) {
            return $this->services;
        }

        if (! Schema::hasTable('services')) {
            return $this->services = collect(config('home.services', []));
        }

        $services = Service::query()->active()->orderBy('display_order')->get();

        if ($services->isEmpty()) {
            return $this->services = collect(config('home.services', []));
        }

        return $this->services = $services->map(function (Service $service): array {
            $key = $this->serviceKey($service->slug);

            return [
                'key' => $key,
                'slug' => $service->slug,
                'icon' => $service->icon ?: 'clipboard-check',
                'bookable' => $service->slug !== 'road-safety',
                'title' => $service->localized('title'),
                'description' => $service->localized('short_description'),
                'full_description' => $service->localized('full_description'),
            ];
        })->values();
    }

    /**
     * Return tariff rows in the legacy preview shape.
     */
    public function tariffPreview(): Collection
    {
        if ($this->tariffs instanceof Collection) {
            return $this->tariffs;
        }

        if (! Schema::hasTable('tariffs')) {
            return $this->tariffs = collect(config('home.tariff_preview', []));
        }

        $tariffs = Tariff::query()
            ->active()
            ->bookable()
            ->effective()
            ->orderBy('display_order')
            ->get();

        if ($tariffs->isEmpty()) {
            return $this->tariffs = collect(config('home.tariff_preview', []));
        }

        return $this->tariffs = $tariffs->values()->map(function (Tariff $tariff, int $index): array {
            $number = $index + 1;
            $category = "Category {$tariff->category_code}";
            $categoryFr = "Categorie {$tariff->category_code}";
            $validity = str_pad((string) $tariff->validity_value, 2, '0', STR_PAD_LEFT);
            $price = number_format($tariff->price_fcfa, 0, ',', ' ').' FCFA';

            return [
                'number' => $number,
                'filter_id' => $this->tariffFilterId($tariff->category_slug),
                'category_en' => $category,
                'category_fr' => $categoryFr,
                'vehicle_type_en' => $tariff->name_en,
                'vehicle_type_fr' => $tariff->name_fr,
                'price' => $price,
                'validity_en' => "{$validity} months",
                'validity_fr' => "{$validity} mois",
                'test_type_en' => $this->tariffTestType($tariff),
                'test_type_fr' => $this->tariffTestType($tariff, 'fr'),
                'documents_en' => 'Registration, insurance (see full tariffs page)',
                'documents_fr' => 'Carte grise, assurance (voir page Tarifs)',
                'category_slug' => $tariff->category_slug,
            ];
        });
    }

    /**
     * Return public careers page payload.
     *
     * @return array<string, mixed>
     */
    public function careersPayload(?string $requestedVacancy = null): array
    {
        $generalApplicationEmail = $this->setting('careers_general_application_email')
            ?: ($this->headquarters()['email'] ?? null);
        $finderLabels = __('careers.finder');
        $statusLabels = $finderLabels['status'];
        $openStatuses = [
            CareerPostStatus::PUBLISHED->value,
            CareerPostStatus::CLOSING_SOON->value,
        ];

        $visibleVacancies = collect();

        if (Schema::hasTable('career_posts')) {
            $visibleVacancies = CareerPost::query()
                ->with(['department', 'center'])
                ->open()
                ->orderBy('display_order')
                ->latest('published_at')
                ->get()
                ->map(function (CareerPost $post) use ($generalApplicationEmail, $openStatuses, $statusLabels): array {
                    $recipient = $post->application_email ?: $generalApplicationEmail;
                    $title = $post->localized('title');
                    $reference = $post->reference;
                    $subject = $post->application_subject
                        ? strtr($post->application_subject, ['{title}' => $title, '{reference}' => $reference])
                        : __('careers.vacancies.mailto_subject', ['title' => $title, 'reference' => $reference]);
                    $body = $post->localized('application_instructions')
                        ?: __('careers.vacancies.mailto_body', ['title' => $title, 'reference' => $reference]);
                    $status = $post->status->value;
                    $department = $post->department;
                    $center = $post->center;
                    $deadline = $post->closes_at
                        ? $post->closes_at->translatedFormat('M j, Y')
                        : (app()->getLocale() === 'fr' ? 'Non precisee' : 'Not specified');

                    $vacancy = [
                        'slug' => $post->slug,
                        'reference' => $reference,
                        'title' => $title,
                        'department_key' => $department?->slug ?? 'general',
                        'department' => $department?->localized('name') ?? (app()->getLocale() === 'fr' ? 'General' : 'General'),
                        'center_key' => $center?->slug ?? 'all-centers',
                        'center' => $center?->localized('name') ?? __('careers.finder.all_centers'),
                        'employment_type_key' => $post->employment_type ?: 'full-time',
                        'employment_type' => $this->employmentTypeLabel($post->employment_type),
                        'deadline' => $deadline,
                        'positions' => $post->vacancies_count,
                        'summary' => $post->localized('summary'),
                        'role_purpose' => $post->localized('description'),
                        'responsibilities' => $this->listFromText($post->localized('responsibilities')),
                        'essential' => $this->listFromText($post->localized('requirements')),
                        'preferred' => $this->listFromText($post->localized('preferred_requirements')),
                        'skills' => $this->listFromText($post->localized('skills')),
                        'documents' => $this->listFromText($post->localized('application_documents')),
                        'status' => $status,
                        'icon' => $department?->icon ?: 'briefcase',
                        'application_email' => $recipient,
                        'status_label' => $statusLabels[$status] ?? $status,
                        'deadline_sentence' => __('careers.finder.deadline_sentence', ['date' => $deadline]),
                        'card_status_label' => in_array($status, $openStatuses, true)
                            ? __('careers.finder.card_deadline', ['date' => $deadline])
                            : __('careers.finder.not_currently_open'),
                        'detail_url' => route('careers.index').'?vacancy='.urlencode($post->slug),
                        'mailto' => $recipient && $post->allow_email_application && in_array($status, $openStatuses, true)
                            ? 'mailto:'.$recipient.'?'.http_build_query([
                                'subject' => $subject,
                                'body' => $body,
                            ], '', '&', PHP_QUERY_RFC3986)
                            : null,
                    ];

                    $vacancy['search_index'] = implode(' ', array_filter([
                        $vacancy['title'],
                        $vacancy['department'],
                        $vacancy['center'],
                        $vacancy['employment_type'],
                        $vacancy['reference'],
                        $vacancy['summary'],
                        $vacancy['role_purpose'],
                        ...$vacancy['responsibilities'],
                        ...$vacancy['essential'],
                        ...$vacancy['preferred'],
                        ...$vacancy['skills'],
                        ...$vacancy['documents'],
                    ]));

                    return $vacancy;
                })
                ->values();
        }

        $filterDepartments = $visibleVacancies
            ->map(fn (array $vacancy): array => ['key' => $vacancy['department_key'], 'label' => $vacancy['department']])
            ->unique('key')
            ->values();
        $filterCenters = $visibleVacancies
            ->map(fn (array $vacancy): array => ['key' => $vacancy['center_key'], 'label' => $vacancy['center']])
            ->unique('key')
            ->values();
        $filterEmploymentTypes = $visibleVacancies
            ->map(fn (array $vacancy): array => ['key' => $vacancy['employment_type_key'], 'label' => $vacancy['employment_type']])
            ->unique('key')
            ->values();
        $initialVacancySlug = $visibleVacancies->contains('slug', $requestedVacancy)
            ? $requestedVacancy
            : ($visibleVacancies->first()['slug'] ?? null);

        return [
            'generalApplicationEmail' => $generalApplicationEmail,
            'generalApplicationMailto' => $generalApplicationEmail
                ? 'mailto:'.$generalApplicationEmail.'?'.http_build_query([
                    'subject' => __('careers.mailto.subject'),
                    'body' => __('careers.mailto.body'),
                ], '', '&', PHP_QUERY_RFC3986)
                : '#',
            'finderLabels' => $finderLabels,
            'visibleVacancies' => $visibleVacancies,
            'hasOpenVacancies' => $visibleVacancies->contains(fn (array $vacancy): bool => in_array($vacancy['status'], $openStatuses, true)),
            'filterDepartments' => $filterDepartments,
            'filterCenters' => $filterCenters,
            'filterEmploymentTypes' => $filterEmploymentTypes,
            'initialVacancySlug' => $initialVacancySlug,
        ];
    }

    public function legalPage(string $slug): ?Page
    {
        if (! Schema::hasTable('pages')) {
            return null;
        }

        return Page::query()->published()->where('slug', $slug)->first();
    }

    public function setting(string $key): ?string
    {
        if (! Schema::hasTable('site_settings')) {
            return null;
        }

        $setting = SiteSetting::query()->key($key)->first();

        return filled($setting?->value) ? $setting->value : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function mapCenter(Center $center): array
    {
        $status = $center->status === CenterStatus::ACTIVE ? 'operational' : 'under_construction';
        $contacts = $center->contacts;
        $phones = $contacts->where('type.value', 'phone')->pluck('value')->values();
        $email = $contacts->where('type.value', 'email')->first()?->value;
        $hours = $this->hoursText($center);

        return [
            'slug' => $center->slug,
            'name' => $center->localized('name'),
            'name_suffix_en' => '',
            'name_suffix_fr' => '',
            'city' => $center->localized('city'),
            'region' => $center->localized('region'),
            'status' => $status,
            'is_headquarters' => $center->is_headquarters,
            'address' => $center->localized('address'),
            'postal_address' => $center->postal_address,
            'landmark' => $center->nearby_landmark,
            'email' => $email,
            'phones' => $phones->all(),
            'latitude' => $center->latitude ? (float) $center->latitude : null,
            'longitude' => $center->longitude ? (float) $center->longitude : null,
            'hours_en' => $hours['en'],
            'hours_fr' => $hours['fr'],
            'hours_lines_en' => $hours['lines_en'],
            'hours_lines_fr' => $hours['lines_fr'],
            'institutional_label_en' => $center->is_headquarters ? 'NACHO Administrative Headquarters' : null,
            'institutional_label_fr' => $center->is_headquarters ? 'Siege administratif NACHO' : null,
            'support_note_en' => $center->description_en,
            'support_note_fr' => $center->description_fr,
            'services' => $center->services->pluck('slug')->all(),
            'booking_key' => $center->slug,
            'maps_url' => $center->google_maps_url,
            'expansion_phase_en' => $center->expansion_phase,
            'expansion_phase_fr' => $center->expansion_phase,
            'expansion_target_en' => $center->target_date_text_en,
            'expansion_target_fr' => $center->target_date_text_fr,
            'expansion_last_updated_en' => $center->expansion_updated_at?->translatedFormat('F Y'),
            'expansion_last_updated_fr' => $center->expansion_updated_at?->translatedFormat('F Y'),
            'featured_image' => $center->featured_image,
            'booking_enabled' => $center->booking_enabled,
        ];
    }

    /**
     * @return array{en: ?string, fr: ?string, lines_en: array<int, string>, lines_fr: array<int, string>}
     */
    private function hoursText(Center $center): array
    {
        $linesEn = [];
        $linesFr = [];

        foreach ($center->hours as $hour) {
            if ($hour->is_closed || ! $hour->opens_at || ! $hour->closes_at) {
                continue;
            }

            $dayEn = Str::title(str_replace('_', ' ', $hour->day_of_week));
            $dayFr = $this->dayLabelFr($hour->day_of_week);
            $openEn = $this->timeLabel($hour->opens_at, 'en');
            $closeEn = $this->timeLabel($hour->closes_at, 'en');
            $openFr = $this->timeLabel($hour->opens_at, 'fr');
            $closeFr = $this->timeLabel($hour->closes_at, 'fr');

            $linesEn[] = "{$openEn} - {$closeEn} ({$dayEn})";
            $linesFr[] = "{$openFr} - {$closeFr} ({$dayFr})";
        }

        return [
            'en' => $linesEn ? implode(' · ', $linesEn) : null,
            'fr' => $linesFr ? implode(' · ', $linesFr) : null,
            'lines_en' => $linesEn,
            'lines_fr' => $linesFr,
        ];
    }

    private function timeLabel(?string $time, string $locale): string
    {
        if (! $time) {
            return '';
        }

        [$hour, $minute] = explode(':', substr($time, 0, 5));

        if ($locale === 'fr') {
            return ltrim($hour, '0').'h'.$minute;
        }

        $hourInt = (int) $hour;
        $suffix = $hourInt >= 12 ? 'PM' : 'AM';
        $displayHour = $hourInt % 12 ?: 12;

        return "{$displayHour}:{$minute} {$suffix}";
    }

    private function phoneHref(string $phone): string
    {
        return preg_replace('/[^\d+]/', '', $phone) ?: $phone;
    }

    private function serviceKey(string $slug): string
    {
        return match ($slug) {
            'periodic-inspection' => 'periodic',
            'light-vehicles' => 'light',
            'heavy-vehicles' => 'heavy',
            'counter-visit' => 'counter',
            'pre-purchase' => 'pre_purchase',
            'road-safety' => 'road_safety',
            default => Str::slug($slug, '_'),
        };
    }

    private function tariffFilterId(string $slug): string
    {
        return match ($slug) {
            'category-a-taxi' => 'taxi',
            'category-b-private' => 'private',
            'category-b1-pickup' => 'pickup',
            'category-c-minibus', 'category-c-coaster' => 'bus',
            'category-d-heavy-utility' => 'truck',
            default => 'other',
        };
    }

    private function tariffTestType(Tariff $tariff, string $locale = 'en'): string
    {
        $all = $locale === 'fr' ? 'Tous' : 'All';
        $allExceptSuspension = $locale === 'fr' ? 'Tous sauf suspension' : 'All except Suspension';

        return in_array($tariff->category_slug, ['category-c-coaster', 'category-d-heavy-utility', 'category-d-other-engins'], true)
            ? $allExceptSuspension
            : $all;
    }

    private function dayLabelFr(string $day): string
    {
        return [
            'monday' => 'Lundi',
            'tuesday' => 'Mardi',
            'wednesday' => 'Mercredi',
            'thursday' => 'Jeudi',
            'friday' => 'Vendredi',
            'saturday' => 'Samedi',
            'sunday' => 'Dimanche',
        ][$day] ?? Str::title($day);
    }

    private function weekdayOrderSql(): string
    {
        return "case day_of_week when 'monday' then 1 when 'tuesday' then 2 when 'wednesday' then 3 when 'thursday' then 4 when 'friday' then 5 when 'saturday' then 6 when 'sunday' then 7 else 8 end";
    }

    private function employmentTypeLabel(?string $type): string
    {
        $locale = app()->getLocale();

        return match ($type) {
            'part-time' => $locale === 'fr' ? 'Temps partiel' : 'Part-time',
            'contract' => $locale === 'fr' ? 'Contrat' : 'Contract',
            'internship' => $locale === 'fr' ? 'Stage' : 'Internship',
            'graduate-trainee-placement', 'graduate-trainee' => $locale === 'fr' ? 'Placement diplome ou stagiaire' : 'Graduate or Trainee Placement',
            default => $locale === 'fr' ? 'Temps plein' : 'Full-time',
        };
    }

    /**
     * @return array<int, string>
     */
    private function listFromText(?string $text): array
    {
        if (! filled($text)) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n|;/', $text))
            ->map(fn (string $line): string => trim($line, " \t\n\r\0\x0B-•"))
            ->filter()
            ->values()
            ->all();
    }
}
