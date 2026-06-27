<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogCategoryRequest;
use App\Models\BlogCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $categories = BlogCategory::query()
            ->withCount('posts')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('name_en', 'like', "%{$search}%")
                    ->orWhere('name_fr', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description_en', 'like', "%{$search}%")
                    ->orWhere('description_fr', 'like', "%{$search}%");
            }))
            ->orderBy('name_en')
            ->paginate(12)
            ->withQueryString();

        return view('admin.blog-categories.index', [
            'categories' => $categories,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.blog-categories.create', [
            'category' => new BlogCategory,
        ]);
    }

    public function store(BlogCategoryRequest $request): RedirectResponse
    {
        $category = BlogCategory::query()->create($request->categoryAttributes());

        return redirect()
            ->route('admin.blog-categories.show', $category)
            ->with('status', 'Blog category created.');
    }

    public function show(BlogCategory $blogCategory): View
    {
        $blogCategory->loadCount('posts');
        $blogCategory->load([
            'posts' => fn ($query) => $query->latest('published_at')->latest(),
        ]);

        return view('admin.blog-categories.show', [
            'category' => $blogCategory,
        ]);
    }

    public function edit(BlogCategory $blogCategory): View
    {
        return view('admin.blog-categories.edit', [
            'category' => $blogCategory,
        ]);
    }

    public function update(BlogCategoryRequest $request, BlogCategory $blogCategory): RedirectResponse
    {
        $blogCategory->update($request->categoryAttributes());

        return redirect()
            ->route('admin.blog-categories.show', $blogCategory)
            ->with('status', 'Blog category updated.');
    }

    public function destroy(BlogCategory $blogCategory): RedirectResponse
    {
        $blogCategory->delete();

        return redirect()
            ->route('admin.blog-categories.index')
            ->with('status', 'Blog category deleted.');
    }
}
