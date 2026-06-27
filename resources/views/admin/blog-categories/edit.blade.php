<x-admin-layout title="Edit Blog Category">
    <div class="space-y-5">
        <a href="{{ route('admin.blog-categories.show', $category) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to category</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">{{ $category->slug }}</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Edit {{ $category->name_en }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.blog-categories.update', $category) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.blog-categories.partials.form', [
                'category' => $category,
                'submitLabel' => 'Save changes',
                'cancelUrl' => route('admin.blog-categories.show', $category),
            ])
        </form>
    </div>
</x-admin-layout>
