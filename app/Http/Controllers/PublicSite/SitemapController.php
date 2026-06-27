<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\CareerPost;
use App\Models\Center;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $entries = $this->entries();
        $xml = view('seo.sitemap', ['entries' => $entries])->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: ?string, changefreq: string, priority: string}>
     */
    private function entries(): Collection
    {
        $entries = collect([
            $this->entry(route('home'), 'daily', '1.0'),
            $this->entry(route('about'), 'monthly', '0.8'),
            $this->entry(route('centers.index'), 'weekly', '0.9', $this->latestDate(Center::class, 'centers', 'active')),
            $this->entry(route('services.index'), 'weekly', '0.8', $this->latestDate(Service::class, 'services', 'active')),
            $this->entry(route('book-inspection'), 'weekly', '0.8'),
            $this->entry(route('tariffs'), 'weekly', '0.8'),
            $this->entry(route('inspection-process'), 'monthly', '0.7'),
            $this->entry(route('blog.index'), 'weekly', '0.6', $this->latestDate(BlogPost::class, 'blog_posts', 'published')),
            $this->entry(route('careers.index'), 'weekly', '0.7', $this->latestDate(CareerPost::class, 'career_posts', 'open')),
            $this->entry(route('contact'), 'monthly', '0.7'),
            $this->entry(route('compliance'), 'monthly', '0.5'),
            $this->entry(route('legal.privacy'), 'yearly', '0.3', $this->pageDate('privacy-policy')),
            $this->entry(route('legal.terms'), 'yearly', '0.3', $this->pageDate('terms-and-conditions')),
            $this->entry(route('legal.cookies'), 'yearly', '0.3', $this->pageDate('cookie-policy')),
            $this->entry(route('legal.notice'), 'yearly', '0.3', $this->pageDate('legal-notice')),
        ]);

        return $entries
            ->merge($this->serviceDetailEntries())
            ->merge($this->blogDetailEntries())
            ->merge($this->careerDeepLinks())
            ->unique('loc')
            ->values();
    }

    private function entry(string $loc, string $changefreq, string $priority, ?Carbon $lastmod = null): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod?->toDateString(),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    private function latestDate(string $model, string $table, string $scope): ?Carbon
    {
        if (! Schema::hasTable($table)) {
            return null;
        }

        $query = $model::query();

        if (method_exists($model, 'scope'.ucfirst($scope))) {
            $query->{$scope}();
        }

        $updatedAt = $query->latest('updated_at')->value('updated_at');

        return $updatedAt ? Carbon::parse($updatedAt) : null;
    }

    private function pageDate(string $slug): ?Carbon
    {
        if (! Schema::hasTable('pages')) {
            return null;
        }

        $updatedAt = Page::query()
            ->published()
            ->where('slug', $slug)
            ->latest('updated_at')
            ->value('updated_at');

        return $updatedAt ? Carbon::parse($updatedAt) : null;
    }

    private function serviceDetailEntries(): Collection
    {
        if (! Route::has('services.show') || ! Schema::hasTable('services')) {
            return collect();
        }

        return Service::query()
            ->active()
            ->orderBy('display_order')
            ->get()
            ->map(fn (Service $service): array => $this->entry(route('services.show', $service), 'monthly', '0.7', $service->updated_at));
    }

    private function blogDetailEntries(): Collection
    {
        if (! Route::has('blog.show') || ! Schema::hasTable('blog_posts')) {
            return collect();
        }

        return BlogPost::query()
            ->published()
            ->latest('published_at')
            ->get()
            ->map(fn (BlogPost $post): array => $this->entry(route('blog.show', $post), 'monthly', '0.6', $post->updated_at));
    }

    private function careerDeepLinks(): Collection
    {
        if (! Schema::hasTable('career_posts')) {
            return collect();
        }

        return CareerPost::query()
            ->open()
            ->latest('published_at')
            ->get()
            ->map(fn (CareerPost $post): array => $this->entry(route('careers.index', ['vacancy' => $post->slug]), 'weekly', '0.6', $post->updated_at));
    }
}
