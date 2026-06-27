@php
    $statusClasses = [
        'draft' => 'bg-gray-100 text-gray-700',
        'published' => 'bg-green-50 text-green-700',
        'archived' => 'bg-red-50 text-red-700',
    ];

    $summaryCards = [
        ['label' => 'Total posts', 'value' => $counts['total'], 'icon' => 'newspaper'],
        ['label' => 'Drafts', 'value' => $counts['draft'], 'icon' => 'file-text'],
        ['label' => 'Published', 'value' => $counts['published'], 'icon' => 'activity'],
        ['label' => 'Archived', 'value' => $counts['archived'], 'icon' => 'archive'],
    ];
@endphp

<x-admin-layout title="Blog">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-500">Manage road-safety education and NACHO news content</p>
                <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Blog management</h2>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.blog-categories.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
                    <x-lucide-tag class="h-4 w-4" aria-hidden="true" />
                    <span>Categories</span>
                </a>
                @adminCan('blog.create')
                    <a href="{{ route('admin.blog-posts.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                        <x-lucide-plus class="h-4 w-4" aria-hidden="true" />
                        <span>Add post</span>
                    </a>
                @endadminCan
            </div>
        </div>

        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($summaryCards as $card)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-500">{{ $card['label'] }}</p>
                            <p class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($card['value']) }}</p>
                        </div>
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                            <x-dynamic-component :component="'lucide-' . $card['icon']" class="h-5 w-5" aria-hidden="true" />
                        </span>
                    </div>
                </article>
            @endforeach
        </section>

        <form method="GET" action="{{ route('admin.blog-posts.index') }}" class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm lg:grid-cols-[minmax(0,1fr)_12rem_14rem_auto]">
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Search</span>
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Title, slug, or excerpt"
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary"
                >
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Status</span>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $case)
                        <option value="{{ $case->value }}" @selected($status === $case->value)>{{ str($case->value)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Category</span>
                <select name="blog_category_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>{{ $category->name_en }}</option>
                    @endforeach
                </select>
            </label>

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-800 hover:bg-gray-50">
                    <x-lucide-search class="h-4 w-4" aria-hidden="true" />
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.blog-posts.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-gray-200 px-3 py-2 text-sm font-bold text-gray-600 hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>

        <div class="grid gap-3 lg:hidden">
            @forelse ($posts as $post)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="break-all text-xs font-bold uppercase text-nacho-primary">{{ $post->slug }}</p>
                            <h3 class="mt-1 break-words text-sm font-bold text-gray-950">{{ $post->title_en }}</h3>
                            <p class="mt-1 text-xs font-semibold text-gray-500">{{ $post->category?->name_en ?: 'Uncategorized' }} · {{ $post->published_at?->format('M j, Y') ?: 'Not scheduled' }}</p>
                        </div>
                        <span @class([
                            'shrink-0 rounded-full px-2.5 py-1 text-xs font-bold',
                            $statusClasses[$post->status->value] ?? 'bg-gray-100 text-gray-600',
                        ])>
                            {{ str($post->status->value)->replace('_', ' ')->title() }}
                        </span>
                    </div>

                    <p class="mt-3 break-words text-sm text-gray-700">{{ str($post->excerpt_en ?: $post->content_en)->limit(150) }}</p>

                    <div class="mt-4 flex flex-wrap justify-end gap-2">
                        <a href="{{ route('admin.blog-posts.show', $post) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                            <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                            <span>View</span>
                        </a>
                        @adminCan('blog.update')
                            <a href="{{ route('admin.blog-posts.edit', $post) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                                <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                                <span>Edit</span>
                            </a>
                        @endadminCan
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-10 text-center text-sm font-semibold text-gray-500 shadow-sm">
                    No blog posts match the current filters.
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm lg:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th scope="col" class="px-4 py-3">Post</th>
                            <th scope="col" class="px-4 py-3">Category</th>
                            <th scope="col" class="px-4 py-3">Author</th>
                            <th scope="col" class="px-4 py-3">Published</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($posts as $post)
                            <tr class="align-top">
                                <td class="max-w-md px-4 py-4">
                                    <div class="font-bold text-gray-950">{{ $post->title_en }}</div>
                                    <div class="mt-1 break-all text-gray-500">{{ $post->slug }}</div>
                                    <div class="mt-1 break-words text-xs text-gray-500">{{ str($post->excerpt_en ?: $post->content_en)->limit(120) }}</div>
                                </td>
                                <td class="px-4 py-4 text-gray-700">{{ $post->category?->name_en ?: 'Uncategorized' }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $post->author?->name ?: 'Not set' }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $post->published_at?->format('M j, Y') ?: 'Not scheduled' }}</td>
                                <td class="px-4 py-4">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                        $statusClasses[$post->status->value] ?? 'bg-gray-100 text-gray-600',
                                    ])>
                                        {{ str($post->status->value)->replace('_', ' ')->title() }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.blog-posts.show', $post) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-950">
                                            <span class="sr-only">View {{ $post->title_en }}</span>
                                            <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                                        </a>
                                        @adminCan('blog.update')
                                            <a href="{{ route('admin.blog-posts.edit', $post) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-950">
                                                <span class="sr-only">Edit {{ $post->title_en }}</span>
                                                <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                                            </a>
                                        @endadminCan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm font-semibold text-gray-500">
                                    No blog posts match the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $posts->links() }}
    </div>
</x-admin-layout>
