<x-admin-layout title="Edit Career Vacancy">
    <div class="space-y-5">
        <a href="{{ route('admin.career-posts.show', $post) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to vacancy</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">{{ $post->reference }}</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Edit {{ $post->title_en }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.career-posts.update', $post) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.career-posts.partials.form', [
                'post' => $post,
                'submitLabel' => 'Save changes',
                'cancelUrl' => route('admin.career-posts.show', $post),
            ])
        </form>
    </div>
</x-admin-layout>
