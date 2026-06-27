<x-admin-layout title="Edit Media">
    <div class="space-y-5">
        <a href="{{ route('admin.media.show', $media) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to media item</span>
        </a>

        <div>
            <p class="break-all text-sm font-semibold text-gray-500">{{ $media->file_path }}</p>
            <h2 class="mt-1 break-words text-xl font-bold tracking-normal text-gray-950">Edit {{ $media->file_name }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.media.update', $media) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.media.partials.metadata-form', [
                'media' => $media,
                'submitLabel' => 'Save changes',
                'cancelUrl' => route('admin.media.show', $media),
            ])
        </form>
    </div>
</x-admin-layout>
