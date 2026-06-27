<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));

        $pages = Page::query()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('title_en', 'like', "%{$search}%")
                    ->orWhere('title_fr', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('content_en', 'like', "%{$search}%")
                    ->orWhere('content_fr', 'like', "%{$search}%");
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderByRaw("CASE slug WHEN 'privacy-policy' THEN 1 WHEN 'terms-and-conditions' THEN 2 WHEN 'cookie-policy' THEN 3 WHEN 'legal-notice' THEN 4 ELSE 99 END")
            ->orderBy('title_en')
            ->paginate(12)
            ->withQueryString();

        return view('admin.pages.index', [
            'pages' => $pages,
            'search' => $search,
            'status' => $status,
            'statuses' => ContentStatus::cases(),
            'counts' => $this->counts(),
            'publicRoutes' => $this->publicRoutes(),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.create', [
            'page' => new Page([
                'status' => ContentStatus::DRAFT,
            ]),
            'statuses' => ContentStatus::cases(),
            'publicRoutes' => $this->publicRoutes(),
        ]);
    }

    public function store(PageRequest $request): RedirectResponse
    {
        $page = Page::query()->create($request->pageAttributes());

        return redirect()
            ->route('admin.pages.show', $page)
            ->with('status', 'Page created.');
    }

    public function show(Page $page): View
    {
        return view('admin.pages.show', [
            'page' => $page,
            'publicUrl' => $this->publicUrl($page),
        ]);
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', [
            'page' => $page,
            'statuses' => ContentStatus::cases(),
            'publicRoutes' => $this->publicRoutes(),
        ]);
    }

    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        $page->update($request->pageAttributes());

        return redirect()
            ->route('admin.pages.show', $page)
            ->with('status', 'Page updated.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->update([
            'status' => ContentStatus::ARCHIVED,
        ]);

        return redirect()
            ->route('admin.pages.index')
            ->with('status', 'Page archived.');
    }

    /**
     * @return array<string, int>
     */
    private function counts(): array
    {
        return [
            'total' => Page::query()->count(),
            'draft' => Page::query()->where('status', ContentStatus::DRAFT->value)->count(),
            'published' => Page::query()->where('status', ContentStatus::PUBLISHED->value)->count(),
            'archived' => Page::query()->where('status', ContentStatus::ARCHIVED->value)->count(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function publicRoutes(): array
    {
        return [
            'privacy-policy' => 'legal.privacy',
            'terms-and-conditions' => 'legal.terms',
            'cookie-policy' => 'legal.cookies',
            'legal-notice' => 'legal.notice',
        ];
    }

    private function publicUrl(Page $page): ?string
    {
        $route = $this->publicRoutes()[$page->slug] ?? null;

        if (! $route || ! Route::has($route)) {
            return null;
        }

        return route($route);
    }
}
