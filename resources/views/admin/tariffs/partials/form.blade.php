<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Category</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Category code</span>
            <input name="category_code" value="{{ old('category_code', $tariff->category_code) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('category_code')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Category slug</span>
            <input name="category_slug" value="{{ old('category_slug', $tariff->category_slug) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('category_slug')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Name EN</span>
            <input name="name_en" value="{{ old('name_en', $tariff->name_en) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('name_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Name FR</span>
            <input name="name_fr" value="{{ old('name_fr', $tariff->name_fr) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('name_fr')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Description EN</span>
            <textarea name="description_en" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('description_en', $tariff->description_en) }}</textarea>
            <x-input-error :messages="$errors->get('description_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Description FR</span>
            <textarea name="description_fr" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('description_fr', $tariff->description_fr) }}</textarea>
            <x-input-error :messages="$errors->get('description_fr')" class="mt-2" />
        </label>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Pricing</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-3">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Price FCFA</span>
            <input type="number" min="0" name="price_fcfa" value="{{ old('price_fcfa', $tariff->price_fcfa) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('price_fcfa')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Validity value</span>
            <input type="number" min="1" name="validity_value" value="{{ old('validity_value', $tariff->validity_value) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('validity_value')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Validity unit</span>
            <select name="validity_unit" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                @foreach ($validityUnits as $unit => $label)
                    <option value="{{ $unit }}" @selected(old('validity_unit', $tariff->validity_unit ?: 'months') === $unit)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('validity_unit')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Minimum weight kg</span>
            <input type="number" min="0" name="minimum_weight_kg" value="{{ old('minimum_weight_kg', $tariff->minimum_weight_kg) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('minimum_weight_kg')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Maximum weight kg</span>
            <input type="number" min="0" name="maximum_weight_kg" value="{{ old('maximum_weight_kg', $tariff->maximum_weight_kg) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('maximum_weight_kg')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Vehicle icon</span>
            <select name="vehicle_icon" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                @foreach ($icons as $icon => $label)
                    <option value="{{ $icon }}" @selected(old('vehicle_icon', $tariff->vehicle_icon ?: \App\Models\Tariff::DEFAULT_VEHICLE_ICON) === $icon)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('vehicle_icon')" class="mt-2" />
        </label>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Publication</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Effective date</span>
            <input type="date" name="effective_date" value="{{ old('effective_date', $tariff->effective_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('effective_date')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Expiry date</span>
            <input type="date" name="expiry_date" value="{{ old('expiry_date', $tariff->expiry_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Regulatory reference</span>
            <input name="regulatory_reference" value="{{ old('regulatory_reference', $tariff->regulatory_reference) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('regulatory_reference')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Last verified at</span>
            <input type="datetime-local" name="last_verified_at" value="{{ old('last_verified_at', $tariff->last_verified_at?->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('last_verified_at')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Display order</span>
            <input type="number" min="0" name="display_order" value="{{ old('display_order', $tariff->display_order ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('display_order')" class="mt-2" />
        </label>
        <div class="grid gap-3 sm:grid-cols-2">
            <label class="flex min-h-11 items-center gap-3 rounded-md border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $tariff->is_active)) class="rounded border-gray-300 text-nacho-primary focus:ring-nacho-primary">
                <span>Active on public website</span>
            </label>
            <label class="flex min-h-11 items-center gap-3 rounded-md border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700">
                <input type="checkbox" name="is_bookable" value="1" @checked(old('is_bookable', $tariff->is_bookable)) class="rounded border-gray-300 text-nacho-primary focus:ring-nacho-primary">
                <span>Bookable</span>
            </label>
        </div>
    </div>
</section>

<div class="flex items-center justify-end gap-3">
    <a href="{{ route('admin.tariffs.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
        Cancel
    </a>
    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
        <x-lucide-save class="h-4 w-4" aria-hidden="true" />
        <span>{{ $submitLabel }}</span>
    </button>
</div>
