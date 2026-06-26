<x-admin-layout title="Add Center">
    <div class="space-y-5">
        <a href="{{ route('admin.centers.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to centers</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">Create a new inspection location</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Add center</h2>
        </div>

        <form method="POST" action="{{ route('admin.centers.store') }}" class="space-y-5">
            @csrf
            @include('admin.centers.partials.form', [
                'center' => $center,
                'services' => $services,
                'selectedServices' => $selectedServices,
                'statuses' => $statuses,
                'submitLabel' => 'Create center',
            ])
        </form>
    </div>
</x-admin-layout>
