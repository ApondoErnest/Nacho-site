<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Identity</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Name EN</span>
            <input name="name_en" value="{{ old('name_en', $department->name_en) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('name_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Name FR</span>
            <input name="name_fr" value="{{ old('name_fr', $department->name_fr) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('name_fr')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Slug</span>
            <input name="slug" value="{{ old('slug', $department->slug) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Display order</span>
            <input type="number" min="0" name="display_order" value="{{ old('display_order', $department->display_order ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('display_order')" class="mt-2" />
        </label>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Display</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Lucide icon</span>
            <select name="icon" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                @foreach ($icons as $icon => $label)
                    <option value="{{ $icon }}" @selected(old('icon', $department->icon ?: \App\Models\CareerDepartment::DEFAULT_ICON) === $icon)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('icon')" class="mt-2" />
        </label>
        <div class="flex items-end">
            <label class="flex min-h-11 w-full items-center gap-3 rounded-md border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $department->is_active)) class="rounded border-gray-300 text-nacho-primary focus:ring-nacho-primary">
                <span>Active on public careers page</span>
            </label>
        </div>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Description</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Description EN</span>
            <textarea name="description_en" rows="5" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('description_en', $department->description_en) }}</textarea>
            <x-input-error :messages="$errors->get('description_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Description FR</span>
            <textarea name="description_fr" rows="5" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('description_fr', $department->description_fr) }}</textarea>
            <x-input-error :messages="$errors->get('description_fr')" class="mt-2" />
        </label>
    </div>
</section>

<div class="flex items-center justify-end gap-3">
    <a href="{{ $cancelUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
        Cancel
    </a>
    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
        <x-lucide-save class="h-4 w-4" aria-hidden="true" />
        <span>{{ $submitLabel }}</span>
    </button>
</div>
