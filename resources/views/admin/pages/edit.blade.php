<x-admin-layout title="Edit Page">
    <div class="space-y-5">
        <a href="{{ route('admin.pages.show', $page) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to page</span>
        </a>

        <div>
            <p class="break-all text-sm font-semibold text-gray-500">{{ $page->slug }}</p>
            <h2 class="mt-1 break-words text-xl font-bold tracking-normal text-gray-950">Edit {{ $page->title_en }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.pages.partials.form', [
                'page' => $page,
                'submitLabel' => 'Save changes',
                'cancelUrl' => route('admin.pages.show', $page),
            ])
        </form>
    </div>
</x-admin-layout>
