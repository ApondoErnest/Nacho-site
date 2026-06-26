<x-admin-layout title="Edit Service">
    <div class="space-y-5">
        <a href="{{ route('admin.services.show', $service) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to service</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">{{ $service->slug }}</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Edit {{ $service->title_en }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.services.partials.form', [
                'service' => $service,
                'submitLabel' => 'Save changes',
            ])
        </form>
    </div>
</x-admin-layout>
