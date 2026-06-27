<x-admin-layout title="Add Career Vacancy">
    <div class="space-y-5">
        <a href="{{ route('admin.career-posts.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to careers</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">Create an email-only vacancy for the public careers page</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Add career vacancy</h2>
        </div>

        <form method="POST" action="{{ route('admin.career-posts.store') }}" class="space-y-5">
            @csrf
            @include('admin.career-posts.partials.form', [
                'post' => $post,
                'submitLabel' => 'Create vacancy',
                'cancelUrl' => route('admin.career-posts.index'),
            ])
        </form>
    </div>
</x-admin-layout>
