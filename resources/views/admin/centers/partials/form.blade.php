@php
    $selectedServiceIds = collect(old('service_ids', $selectedServices->all() ?? []))->map(fn ($id) => (int) $id);
@endphp

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Identity</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Name EN</span>
            <input name="name_en" value="{{ old('name_en', $center->name_en) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('name_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Name FR</span>
            <input name="name_fr" value="{{ old('name_fr', $center->name_fr) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('name_fr')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Slug</span>
            <input name="slug" value="{{ old('slug', $center->slug) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Display order</span>
            <input type="number" min="0" name="display_order" value="{{ old('display_order', $center->display_order ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('display_order')" class="mt-2" />
        </label>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Location</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">City EN</span>
            <input name="city_en" value="{{ old('city_en', $center->city_en) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('city_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">City FR</span>
            <input name="city_fr" value="{{ old('city_fr', $center->city_fr) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('city_fr')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Region EN</span>
            <input name="region_en" value="{{ old('region_en', $center->region_en) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Region FR</span>
            <input name="region_fr" value="{{ old('region_fr', $center->region_fr) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
        </label>
        <label class="block md:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Address EN</span>
            <textarea name="address_en" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('address_en', $center->address_en) }}</textarea>
        </label>
        <label class="block md:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Address FR</span>
            <textarea name="address_fr" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('address_fr', $center->address_fr) }}</textarea>
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Postal address</span>
            <input name="postal_address" value="{{ old('postal_address', $center->postal_address) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Nearby landmark</span>
            <input name="nearby_landmark" value="{{ old('nearby_landmark', $center->nearby_landmark) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Latitude</span>
            <input name="latitude" value="{{ old('latitude', $center->latitude) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Longitude</span>
            <input name="longitude" value="{{ old('longitude', $center->longitude) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
        </label>
        <label class="block md:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Google Maps URL</span>
            <input type="url" name="google_maps_url" value="{{ old('google_maps_url', $center->google_maps_url) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('google_maps_url')" class="mt-2" />
        </label>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Publishing and Operations</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Status</span>
            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $center->status?->value ?? $center->status) === $status->value)>
                        {{ str($status->value)->replace('_', ' ')->title() }}
                    </option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Featured image path</span>
            <input name="featured_image" value="{{ old('featured_image', $center->featured_image) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
        </label>
        <div class="grid gap-3 md:col-span-2 md:grid-cols-4">
            @foreach ([
                'is_active' => 'Published',
                'booking_enabled' => 'Booking enabled',
                'is_headquarters' => 'Headquarters',
                'is_featured' => 'Featured',
            ] as $field => $label)
                <label class="flex min-h-11 items-center gap-3 rounded-md border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700">
                    <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $center->{$field})) class="rounded border-gray-300 text-nacho-primary focus:ring-nacho-primary">
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Content and Finder Data</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Description EN</span>
            <textarea name="description_en" rows="4" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('description_en', $center->description_en) }}</textarea>
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Description FR</span>
            <textarea name="description_fr" rows="4" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('description_fr', $center->description_fr) }}</textarea>
        </label>
        <label class="block md:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Search keywords</span>
            <textarea name="search_keywords" rows="2" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('search_keywords', $center->search_keywords) }}</textarea>
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Vehicle categories EN</span>
            <textarea name="vehicle_categories_en" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('vehicle_categories_en', $center->vehicle_categories_en) }}</textarea>
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Vehicle categories FR</span>
            <textarea name="vehicle_categories_fr" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('vehicle_categories_fr', $center->vehicle_categories_fr) }}</textarea>
        </label>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Expansion Planning</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Target opening date</span>
            <input type="date" name="target_opening_date" value="{{ old('target_opening_date', $center->target_opening_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Expansion phase</span>
            <input name="expansion_phase" value="{{ old('expansion_phase', $center->expansion_phase) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Target text EN</span>
            <input name="target_date_text_en" value="{{ old('target_date_text_en', $center->target_date_text_en) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Target text FR</span>
            <input name="target_date_text_fr" value="{{ old('target_date_text_fr', $center->target_date_text_fr) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Expansion updated at</span>
            <input type="datetime-local" name="expansion_updated_at" value="{{ old('expansion_updated_at', $center->expansion_updated_at?->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
        </label>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Service Availability</h3>
    </div>
    <div class="grid gap-3 p-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($services as $service)
            <label class="flex min-h-12 items-start gap-3 rounded-md border border-gray-200 px-3 py-3 text-sm text-gray-700">
                <input
                    type="checkbox"
                    name="service_ids[]"
                    value="{{ $service->id }}"
                    @checked($selectedServiceIds->contains($service->id))
                    class="mt-0.5 rounded border-gray-300 text-nacho-primary focus:ring-nacho-primary"
                >
                <span>
                    <span class="block font-bold text-gray-950">{{ $service->title_en }}</span>
                    <span class="block text-xs text-gray-500">{{ $service->slug }}</span>
                </span>
            </label>
        @empty
            <p class="text-sm font-semibold text-gray-500">No services are available yet.</p>
        @endforelse
    </div>
</section>

<div class="flex items-center justify-end gap-3">
    <a href="{{ route('admin.centers.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
        Cancel
    </a>
    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
        <x-lucide-save class="h-4 w-4" aria-hidden="true" />
        <span>{{ $submitLabel }}</span>
    </button>
</div>
