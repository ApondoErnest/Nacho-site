<x-admin-layout title="Blog Categories">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <a href="{{ route('admin.blog-posts.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
                    <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
                    <span>Back to blog</span>
                </a>
                <p class="mt-4 text-sm font-semibold text-gray-500">Organize posts for the public blog</p>
                <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Blog categories</h2>
            </div>

            @adminCan('blog.create')
                <a href="{{ route('admin.blog-categories.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                    <x-lucide-plus class="h-4 w-4" aria-hidden="true" />
                    <span>Add category</span>
                </a>
            @endadminCan
        </div>

        <form method="GET" action="{{ route('admin.blog-categories.index') }}" class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-[minmax(0,1fr)_auto]">
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Search</span>
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Name, slug, or description"
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary"
                >
            </label>

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-800 hover:bg-gray-50">
                    <x-lucide-search class="h-4 w-4" aria-hidden="true" />
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.blog-categories.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-gray-200 px-3 py-2 text-sm font-bold text-gray-600 hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>

        <div class="grid gap-3 lg:hidden">
            @forelse ($categories as $category)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="break-words text-sm font-bold text-gray-950">{{ $category->name_en }}</h3>
                            <p class="mt-1 break-all text-xs font-semibold text-gray-500">{{ $category->slug }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-700">
                            {{ $category->posts_count }} posts
                        </span>
                    </div>

                    <p class="mt-3 break-words text-sm text-gray-700">{{ str($category->description_en)->limit(140) ?: 'No description set.' }}</p>

                    <div class="mt-4 flex flex-wrap justify-end gap-2">
                        <a href="{{ route('admin.blog-categories.show', $category) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                            <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                            <span>View</span>
                        </a>
                        @adminCan('blog.update')
                            <a href="{{ route('admin.blog-categories.edit', $category) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                                <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                                <span>Edit</span>
                            </a>
                        @endadminCan
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-10 text-center text-sm font-semibold text-gray-500 shadow-sm">
                    No blog categories match the current filters.
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm lg:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th scope="col" class="px-4 py-3">Category</th>
                            <th scope="col" class="px-4 py-3">Description</th>
                            <th scope="col" class="px-4 py-3">Posts</th>
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($categories as $category)
                            <tr class="align-top">
                                <td class="px-4 py-4">
                                    <div class="font-bold text-gray-950">{{ $category->name_en }}</div>
                                    <div class="mt-1 break-all text-gray-500">{{ $category->slug }}</div>
                                    <div class="mt-1 text-xs text-gray-500">{{ $category->name_fr }}</div>
                                </td>
                                <td class="max-w-xl px-4 py-4 text-gray-700">{{ str($category->description_en)->limit(140) ?: 'No description set.' }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $category->posts_count }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.blog-categories.show', $category) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-950">
                                            <span class="sr-only">View {{ $category->name_en }}</span>
                                            <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                                        </a>
                                        @adminCan('blog.update')
                                            <a href="{{ route('admin.blog-categories.edit', $category) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-950">
                                                <span class="sr-only">Edit {{ $category->name_en }}</span>
                                                <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                                            </a>
                                        @endadminCan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-sm font-semibold text-gray-500">
                                    No blog categories match the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $categories->links() }}
    </div>
</x-admin-layout>
