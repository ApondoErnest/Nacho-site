<?php

namespace App\Support;

use App\Models\Page;
use Illuminate\Support\Str;

class SeoMeta
{
    /**
     * @var array<string, string>
     */
    private array $routePageMap = [
        'home' => 'home',
        'about' => 'about',
        'centers.index' => 'centers',
        'services.index' => 'services',
        'book-inspection' => 'book_inspection',
        'tariffs' => 'tariffs',
        'inspection-process' => 'inspection_process',
        'blog.index' => 'blog',
        'compliance' => 'compliance',
        'careers.index' => 'careers',
        'contact' => 'contact',
        'legal.privacy' => 'privacy',
        'legal.terms' => 'terms',
        'legal.cookies' => 'cookies',
        'legal.notice' => 'legal_notice',
    ];

    /**
     * @return array<string, mixed>
     */
    public function forCurrentRequest(): array
    {
        $routeName = request()->route()?->getName();
        $page = $this->routePageMap[$routeName] ?? 'default';

        return $this->page($page);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function page(string $page, array $overrides = []): array
    {
        $definition = trans("seo.pages.{$page}");

        if (! is_array($definition)) {
            $definition = trans('seo.pages.default');
        }

        return $this->make(array_merge($definition, $overrides));
    }

    /**
     * @return array<string, mixed>
     */
    public function legalPage(Page $page, string $fallbackPage): array
    {
        $title = $page->localized('seo_title') ?: $page->localized('title');
        $description = $page->localized('meta_description')
            ?: Str::limit(trim(strip_tags($page->localized('content') ?: '')), 155);

        return $this->page($fallbackPage, [
            'title' => $title,
            'description' => $description ?: null,
        ]);
    }

    /**
     * @param  iterable<int, array<string, mixed>>  $centers
     * @return array<string, mixed>
     */
    public function centers(iterable $centers): array
    {
        return $this->page('centers', [
            'jsonLd' => [$this->centersItemList($centers)],
        ]);
    }

    /**
     * @param  iterable<int, array<string, mixed>>  $vacancies
     * @return array<string, mixed>
     */
    public function careers(iterable $vacancies): array
    {
        $schemas = collect($vacancies)
            ->map(fn (array $vacancy): array => $this->jobPosting($vacancy))
            ->values()
            ->all();

        return $this->page('careers', [
            'jsonLd' => $schemas,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function home(array $headquarters): array
    {
        return $this->page('home', [
            'jsonLd' => [$this->organization($headquarters)],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function make(array $data = []): array
    {
        $siteName = __('seo.site_name');
        $title = $this->clean($data['title'] ?? __('seo.pages.default.title'));
        $description = $this->clean($data['description'] ?? __('seo.pages.default.description'));
        $image = $data['image'] ?? __('seo.default_image');
        $canonical = $data['canonical'] ?? url()->current();
        $type = $data['type'] ?? 'website';

        return [
            'title' => $this->titleWithSite($title, $siteName),
            'description' => Str::limit($description, 160, ''),
            'canonical' => $canonical,
            'image' => $this->absoluteAssetUrl($image),
            'type' => $type,
            'siteName' => $siteName,
            'locale' => app()->getLocale() === 'fr' ? 'fr_CM' : 'en_CM',
            'robots' => $data['robots'] ?? 'index,follow',
            'jsonLd' => $this->normalizeJsonLd($data['jsonLd'] ?? []),
        ];
    }

    private function clean(mixed $value): string
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags((string) $value)) ?: '');
    }

    private function titleWithSite(string $title, string $siteName): string
    {
        if (str_contains(Str::lower($title), Str::lower($siteName))) {
            return $title;
        }

        return "{$title} | {$siteName}";
    }

    private function absoluteAssetUrl(?string $path): string
    {
        $path = $path ?: __('seo.default_image');

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function normalizeJsonLd(mixed $schemas): array
    {
        if (! is_array($schemas)) {
            return [];
        }

        $isSingleSchema = isset($schemas['@context']) || isset($schemas['@type']);

        return collect($isSingleSchema ? [$schemas] : $schemas)
            ->filter(fn (mixed $schema): bool => is_array($schema) && $schema !== [])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function organization(array $headquarters): array
    {
        $phones = collect($headquarters['phones'] ?? [])->filter()->values();

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'AutomotiveBusiness',
            'name' => __('seo.organization.name'),
            'url' => route('home'),
            'logo' => asset(ltrim(config('branding.logo'), '/')),
            'image' => $this->absoluteAssetUrl(__('seo.default_image')),
            'slogan' => __('components.slogan'),
            'email' => $headquarters['email'] ?? null,
            'telephone' => $phones->first() ?: ($headquarters['phone_primary'] ?? null),
            'address' => $headquarters['address'] ?? null,
            'areaServed' => [
                '@type' => 'Country',
                'name' => __('seo.organization.area_served'),
            ],
        ]);
    }

    /**
     * @param  iterable<int, array<string, mixed>>  $centers
     * @return array<string, mixed>
     */
    private function centersItemList(iterable $centers): array
    {
        $items = collect($centers)
            ->where('status', 'operational')
            ->values()
            ->map(function (array $center, int $index): array {
                $business = array_filter([
                    '@type' => 'AutomotiveBusiness',
                    'name' => $center['name'] ?? null,
                    'url' => route('centers.index').'#center-card-'.$center['slug'],
                    'address' => $center['address'] ?? null,
                    'telephone' => collect($center['phones'] ?? [])->first(),
                    'email' => $center['email'] ?? null,
                    'geo' => ($center['latitude'] ?? null) && ($center['longitude'] ?? null) ? [
                        '@type' => 'GeoCoordinates',
                        'latitude' => (float) $center['latitude'],
                        'longitude' => (float) $center['longitude'],
                    ] : null,
                ]);

                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'item' => $business,
                ];
            })
            ->all();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => __('seo.structured_data.centers_list'),
            'itemListElement' => $items,
        ];
    }

    /**
     * @param  array<string, mixed>  $vacancy
     * @return array<string, mixed>
     */
    private function jobPosting(array $vacancy): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $vacancy['title'] ?? null,
            'description' => trim(strip_tags(($vacancy['summary'] ?? '').' '.($vacancy['role_purpose'] ?? ''))),
            'identifier' => [
                '@type' => 'PropertyValue',
                'name' => __('seo.organization.name'),
                'value' => $vacancy['reference'] ?? null,
            ],
            'datePosted' => $vacancy['published_at_iso'] ?? null,
            'validThrough' => $vacancy['closes_at_iso'] ?? null,
            'employmentType' => $vacancy['employment_type_key'] ?? null,
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => __('seo.organization.name'),
                'sameAs' => route('home'),
                'logo' => asset(ltrim(config('branding.logo'), '/')),
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'name' => $vacancy['center'] ?? __('careers.finder.all_centers'),
            ],
            'url' => $vacancy['detail_url'] ?? route('careers.index'),
        ]);
    }
}
