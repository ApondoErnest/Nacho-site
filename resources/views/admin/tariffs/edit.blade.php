<x-admin-layout title="Edit Tariff">
    <div class="space-y-5">
        <a href="{{ route('admin.tariffs.show', $tariff) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to tariff</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">{{ $tariff->category_slug }}</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Edit {{ $tariff->name_en }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.tariffs.update', $tariff) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.tariffs.partials.form', [
                'tariff' => $tariff,
                'icons' => $icons,
                'validityUnits' => $validityUnits,
                'submitLabel' => 'Save changes',
            ])
        </form>
    </div>
</x-admin-layout>
