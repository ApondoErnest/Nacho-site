<x-admin-layout title="Upload Media">
    <div class="space-y-5">
        <a href="{{ route('admin.media.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to media</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">Add images and documents to the reusable media library</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Upload media</h2>
        </div>

        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @include('admin.media.partials.upload-form', [
                'submitLabel' => 'Upload media',
                'cancelUrl' => route('admin.media.index'),
            ])
        </form>
    </div>
</x-admin-layout>
