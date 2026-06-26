<x-admin-layout title="Edit Center">
    <div class="space-y-5">
        <a href="{{ route('admin.centers.show', $center) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to center</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">{{ $center->slug }}</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Edit {{ $center->name_en }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.centers.update', $center) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.centers.partials.form', [
                'center' => $center,
                'services' => $services,
                'selectedServices' => $selectedServices,
                'statuses' => $statuses,
                'submitLabel' => 'Save changes',
            ])
        </form>
    </div>
</x-admin-layout>
