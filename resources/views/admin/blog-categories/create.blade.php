<x-admin-layout title="Add Blog Category">
    <div class="space-y-5">
        <a href="{{ route('admin.blog-categories.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to categories</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">Create a grouping for blog posts</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Add blog category</h2>
        </div>

        <form method="POST" action="{{ route('admin.blog-categories.store') }}" class="space-y-5">
            @csrf
            @include('admin.blog-categories.partials.form', [
                'category' => $category,
                'submitLabel' => 'Create category',
                'cancelUrl' => route('admin.blog-categories.index'),
            ])
        </form>
    </div>
</x-admin-layout>
