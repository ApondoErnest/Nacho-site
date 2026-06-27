@php
    $summaryCards = [
        ['label' => 'Total settings', 'value' => $counts['total'], 'icon' => 'settings'],
        ['label' => 'Text settings', 'value' => $counts['text'], 'icon' => 'file-text'],
        ['label' => 'Image paths', 'value' => $counts['image'], 'icon' => 'image'],
        ['label' => 'Toggles', 'value' => $counts['boolean'], 'icon' => 'toggle-left'],
        ['label' => 'Colors', 'value' => $counts['color'], 'icon' => 'palette'],
    ];
@endphp

<x-admin-layout title="Settings">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-500">Manage site-wide identity, contact, careers, tariff, and access settings</p>
                <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Site settings</h2>
            </div>
        </div>

        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ($summaryCards as $card)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-500">{{ $card['label'] }}</p>
                            <p class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($card['value']) }}</p>
                        </div>
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                            <x-dynamic-component :component="'lucide-' . $card['icon']" class="h-5 w-5" aria-hidden="true" />
                        </span>
                    </div>
                </article>
            @endforeach
        </section>

        <form method="POST" action="{{ route('admin.site-settings.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            @foreach ($groupedSettings as $group => $items)
                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-bold tracking-normal text-gray-950">{{ $group }}</h3>
                    </div>
                    <div class="grid gap-4 p-5 lg:grid-cols-2">
                        @foreach ($items as $item)
                            @include('admin.site-settings.partials.field', [
                                'setting' => $item['setting'],
                                'definition' => $item['definition'],
                            ])
                        @endforeach
                    </div>
                </section>
            @endforeach

            <div class="flex items-center justify-end">
                @adminCan('site-settings.update')
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                        <x-lucide-save class="h-4 w-4" aria-hidden="true" />
                        <span>Save settings</span>
                    </button>
                @endadminCan
            </div>
        </form>
    </div>
</x-admin-layout>
