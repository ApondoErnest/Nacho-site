<x-admin-layout title="Add Page">
    <div class="space-y-5">
        <a href="{{ route('admin.pages.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to pages</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">Create bilingual legal or static page copy</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Add page</h2>
        </div>

        <form method="POST" action="{{ route('admin.pages.store') }}" class="space-y-5">
            @csrf
            @include('admin.pages.partials.form', [
                'page' => $page,
                'submitLabel' => 'Create page',
                'cancelUrl' => route('admin.pages.index'),
            ])
        </form>
    </div>
</x-admin-layout>
