<x-admin-layout title="Service Details">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <a href="{{ route('admin.services.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
                    <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
                    <span>Back to services</span>
                </a>
                <div class="mt-4 flex items-center gap-3">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                        <x-dynamic-component :component="'lucide-' . $service->lucideIcon()" class="h-6 w-6" aria-hidden="true" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-gray-500">{{ $service->slug }}</p>
                        <h2 class="mt-1 text-2xl font-bold tracking-normal text-gray-950">{{ $service->title_en }}</h2>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                @adminCan('services.update')
                    <a href="{{ route('admin.services.edit', $service) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                        <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                        <span>Edit</span>
                    </a>
                @endadminCan
                @adminCan('services.delete')
                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Archive this service?');">
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
                ['label' => 'Status', 'value' => $service->is_active ? 'Active' : 'Inactive', 'icon' => 'activity'],
                ['label' => 'Display order', 'value' => $service->display_order, 'icon' => 'arrow-down-up'],
                ['label' => 'Assigned centers', 'value' => $service->centers->count(), 'icon' => 'building-2'],
                ['label' => 'Bookings', 'value' => $service->bookings_count, 'icon' => 'calendar-days'],
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
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Content</h3>
                </div>
                <dl class="grid gap-4 p-5 text-sm md:grid-cols-2">
                    <div>
                        <dt class="font-semibold text-gray-500">French title</dt>
                        <dd class="mt-1 font-bold text-gray-950">{{ $service->title_fr }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Featured image</dt>
                        <dd class="mt-1 text-gray-800">{{ $service->featured_image ?: 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Short description EN</dt>
                        <dd class="mt-1 text-gray-800">{{ $service->short_description_en ?: 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Short description FR</dt>
                        <dd class="mt-1 text-gray-800">{{ $service->short_description_fr ?: 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Full description EN</dt>
                        <dd class="mt-1 text-gray-800">{{ $service->full_description_en ?: 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Full description FR</dt>
                        <dd class="mt-1 text-gray-800">{{ $service->full_description_fr ?: 'Not set' }}</dd>
                    </div>
                </dl>
            </section>

            <aside class="space-y-5">
                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-bold tracking-normal text-gray-950">Assigned centers</h3>
                    </div>
                    <div class="space-y-2 p-5">
                        @forelse ($service->centers as $center)
                            <div class="rounded-md border border-gray-200 px-3 py-2 text-sm">
                                <p class="font-bold text-gray-950">{{ $center->name_en }}</p>
                                <p class="text-xs text-gray-500">{{ $center->pivot->booking_enabled ? 'Bookable' : 'Not bookable' }}</p>
                            </div>
                        @empty
                            <p class="text-sm font-semibold text-gray-500">No centers assigned.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-bold tracking-normal text-gray-950">SEO</h3>
                    </div>
                    <dl class="space-y-3 p-5 text-sm">
                        <div>
                            <dt class="font-semibold text-gray-500">SEO title EN</dt>
                            <dd class="mt-1 text-gray-800">{{ $service->seo_title_en ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Meta description EN</dt>
                            <dd class="mt-1 text-gray-800">{{ $service->meta_description_en ?: 'Not set' }}</dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
    </div>
</x-admin-layout>
