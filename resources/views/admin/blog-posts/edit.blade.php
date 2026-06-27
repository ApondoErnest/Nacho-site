<x-admin-layout title="Edit Blog Post">
    <div class="space-y-5">
        <a href="{{ route('admin.blog-posts.show', $post) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to post</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">{{ $post->slug }}</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Edit {{ $post->title_en }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.blog-posts.update', $post) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.blog-posts.partials.form', [
                'post' => $post,
                'submitLabel' => 'Save changes',
                'cancelUrl' => route('admin.blog-posts.show', $post),
            ])
        </form>
    </div>
</x-admin-layout>
