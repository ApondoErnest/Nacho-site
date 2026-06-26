<x-admin-layout title="Add Tariff">
    <div class="space-y-5">
        <a href="{{ route('admin.tariffs.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to tariffs</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">Create a Master Pricing Console row</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Add tariff</h2>
        </div>

        <form method="POST" action="{{ route('admin.tariffs.store') }}" class="space-y-5">
            @csrf
            @include('admin.tariffs.partials.form', [
                'tariff' => $tariff,
                'icons' => $icons,
                'validityUnits' => $validityUnits,
                'submitLabel' => 'Create tariff',
            ])
        </form>
    </div>
</x-admin-layout>
