<x-admin-layout title="Add Blog Post">
    <div class="space-y-5">
        <a href="{{ route('admin.blog-posts.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to blog</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">Create a bilingual public article</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Add blog post</h2>
        </div>

        <form method="POST" action="{{ route('admin.blog-posts.store') }}" class="space-y-5">
            @csrf
            @include('admin.blog-posts.partials.form', [
                'post' => $post,
                'submitLabel' => 'Create post',
                'cancelUrl' => route('admin.blog-posts.index'),
            ])
        </form>
    </div>
</x-admin-layout>
