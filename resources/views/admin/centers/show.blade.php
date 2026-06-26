<x-admin-layout title="Center Details">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <a href="{{ route('admin.centers.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
                    <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
                    <span>Back to centers</span>
                </a>
                <p class="mt-4 text-sm font-semibold text-gray-500">{{ $center->slug }}</p>
                <h2 class="mt-1 text-2xl font-bold tracking-normal text-gray-950">{{ $center->name_en }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $center->city_en }}{{ $center->region_en ? ', '.$center->region_en : '' }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                @adminCan('centers.update')
                    <a href="{{ route('admin.centers.edit', $center) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                        <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                        <span>Edit</span>
                    </a>
                @endadminCan
                @adminCan('centers.delete')
                    <form method="POST" action="{{ route('admin.centers.destroy', $center) }}" onsubmit="return confirm('Archive this center?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-red-200 bg-white px-4 py-2 text-sm font-bold text-red-700 hover:bg-red-50">
                            <x-lucide-trash-2 class="h-4 w-4" aria-hidden="true" />
                            <span>Archive</span>
                        </button>
                    </form>
                @endadminCan
            </div>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Status', 'value' => str($center->status->value)->replace('_', ' ')->title(), 'icon' => 'activity'],
                ['label' => 'Bookings', 'value' => $center->booking_enabled ? 'Enabled' : 'Disabled', 'icon' => 'calendar-check'],
                ['label' => 'Services', 'value' => $center->services->count(), 'icon' => 'clipboard-check'],
                ['label' => 'Published', 'value' => $center->is_active ? 'Yes' : 'No', 'icon' => 'eye'],
            ] as $summary)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-500">{{ $summary['label'] }}</p>
                            <p class="mt-2 text-xl font-bold text-gray-950">{{ $summary['value'] }}</p>
                        </div>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                            <x-dynamic-component :component="'lucide-' . $summary['icon']" class="h-5 w-5" aria-hidden="true" />
                        </span>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_24rem]">
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Location and Content</h3>
                </div>
                <dl class="grid gap-4 p-5 text-sm md:grid-cols-2">
                    <div>
                        <dt class="font-semibold text-gray-500">French name</dt>
                        <dd class="mt-1 font-bold text-gray-950">{{ $center->name_fr }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Postal address</dt>
                        <dd class="mt-1 text-gray-800">{{ $center->postal_address ?: 'Not set' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="font-semibold text-gray-500">Address EN</dt>
                        <dd class="mt-1 text-gray-800">{{ $center->address_en ?: 'Not set' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="font-semibold text-gray-500">Address FR</dt>
                        <dd class="mt-1 text-gray-800">{{ $center->address_fr ?: 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Coordinates</dt>
                        <dd class="mt-1 text-gray-800">{{ $center->latitude && $center->longitude ? $center->latitude.', '.$center->longitude : 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Maps</dt>
                        <dd class="mt-1">
                            @if ($center->google_maps_url)
                                <a href="{{ $center->google_maps_url }}" class="font-bold text-nacho-primary hover:text-nacho-primary-dark">Open map</a>
                            @else
                                <span class="text-gray-800">Not set</span>
                            @endif
                        </dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="font-semibold text-gray-500">Description EN</dt>
                        <dd class="mt-1 text-gray-800">{{ $center->description_en ?: 'Not set' }}</dd>
                    </div>
                </dl>
            </section>

            <aside class="space-y-5">
                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-bold tracking-normal text-gray-950">Assigned services</h3>
                    </div>
                    <div class="space-y-2 p-5">
                        @forelse ($center->services as $service)
                            <div class="rounded-md border border-gray-200 px-3 py-2 text-sm">
                                <p class="font-bold text-gray-950">{{ $service->title_en }}</p>
                                <p class="text-xs text-gray-500">{{ $service->pivot->booking_enabled ? 'Bookable' : 'Not bookable' }}</p>
                            </div>
                        @empty
                            <p class="text-sm font-semibold text-gray-500">No services assigned.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-bold tracking-normal text-gray-950">Contacts</h3>
                    </div>
                    <div class="space-y-2 p-5">
                        @forelse ($center->contacts as $contact)
                            <div class="rounded-md border border-gray-200 px-3 py-2 text-sm">
                                <p class="font-bold text-gray-950">{{ str($contact->type->value)->title() }} · {{ $contact->value }}</p>
                                <p class="text-xs text-gray-500">{{ $contact->is_primary ? 'Primary' : 'Secondary' }}{{ $contact->is_public ? ' · Public' : ' · Private' }}</p>
                            </div>
                        @empty
                            <p class="text-sm font-semibold text-gray-500">No contacts configured.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-admin-layout>
