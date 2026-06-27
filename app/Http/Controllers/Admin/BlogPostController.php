<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogPostRequest;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));
        $categoryId = trim((string) $request->query('blog_category_id'));

        $posts = BlogPost::query()
            ->with(['category', 'author'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('title_en', 'like', "%{$search}%")
                    ->orWhere('title_fr', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('excerpt_en', 'like', "%{$search}%")
                    ->orWhere('excerpt_fr', 'like', "%{$search}%");
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($categoryId !== '', fn ($query) => $query->where('blog_category_id', $categoryId))
            ->latest('published_at')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.blog-posts.index', [
            'posts' => $posts,
            'search' => $search,
            'status' => $status,
            'categoryId' => $categoryId,
            'statuses' => ContentStatus::cases(),
            'categories' => $this->categories(),
            'counts' => $this->counts(),
        ]);
    }

    public function create(): View
    {
        return view('admin.blog-posts.create', [
            'post' => new BlogPost([
                'status' => ContentStatus::DRAFT,
            ]),
            'statuses' => ContentStatus::cases(),
            'categories' => $this->categories(),
        ]);
    }

    public function store(BlogPostRequest $request): RedirectResponse
    {
        $post = BlogPost::query()->create([
            ...$this->publicationAttributes($request->postAttributes()),
            'author_id' => $request->user()?->id,
        ]);

        return redirect()
            ->route('admin.blog-posts.show', $post)
            ->with('status', 'Blog post created.');
    }

    public function show(BlogPost $blogPost): View
    {
        $blogPost->load(['category', 'author']);

        return view('admin.blog-posts.show', [
            'post' => $blogPost,
        ]);
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.edit', [
            'post' => $blogPost,
            'statuses' => ContentStatus::cases(),
            'categories' => $this->categories(),
        ]);
    }

    public function update(BlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $blogPost->update($this->publicationAttributes($request->postAttributes()));

        return redirect()
            ->route('admin.blog-posts.show', $blogPost)
            ->with('status', 'Blog post updated.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->update([
            'status' => ContentStatus::ARCHIVED,
        ]);

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('status', 'Blog post archived.');
    }

    private function categories()
    {
        return BlogCategory::query()
            ->orderBy('name_en')
            ->get();
    }

    /**
     * @return array<string, int>
     */
    private function counts(): array
    {
        return [
            'total' => BlogPost::query()->count(),
            'draft' => BlogPost::query()->where('status', ContentStatus::DRAFT->value)->count(),
            'published' => BlogPost::query()->where('status', ContentStatus::PUBLISHED->value)->count(),
            'archived' => BlogPost::query()->where('status', ContentStatus::ARCHIVED->value)->count(),
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function publicationAttributes(array $attributes): array
    {
        if (($attributes['status'] ?? null) === ContentStatus::PUBLISHED->value && blank($attributes['published_at'] ?? null)) {
            $attributes['published_at'] = now();
        }

        if (($attributes['status'] ?? null) === ContentStatus::DRAFT->value && blank($attributes['published_at'] ?? null)) {
            $attributes['published_at'] = null;
        }

        return $attributes;
    }
}
