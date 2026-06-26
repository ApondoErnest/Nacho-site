<x-admin-layout title="Add Service">
    <div class="space-y-5">
        <a href="{{ route('admin.services.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to services</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">Create a public service offering</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Add service</h2>
        </div>

        <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-5">
            @csrf
            @include('admin.services.partials.form', [
                'service' => $service,
                'submitLabel' => 'Create service',
            ])
        </form>
    </div>
</x-admin-layout>
