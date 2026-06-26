<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Identity</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Title EN</span>
            <input name="title_en" value="{{ old('title_en', $service->title_en) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('title_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Title FR</span>
            <input name="title_fr" value="{{ old('title_fr', $service->title_fr) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('title_fr')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Slug</span>
            <input name="slug" value="{{ old('slug', $service->slug) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Display order</span>
            <input type="number" min="0" name="display_order" value="{{ old('display_order', $service->display_order ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('display_order')" class="mt-2" />
        </label>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Bilingual Content</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Short description EN</span>
            <textarea name="short_description_en" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('short_description_en', $service->short_description_en) }}</textarea>
            <x-input-error :messages="$errors->get('short_description_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Short description FR</span>
            <textarea name="short_description_fr" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('short_description_fr', $service->short_description_fr) }}</textarea>
            <x-input-error :messages="$errors->get('short_description_fr')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Full description EN</span>
            <textarea name="full_description_en" rows="7" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('full_description_en', $service->full_description_en) }}</textarea>
            <x-input-error :messages="$errors->get('full_description_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Full description FR</span>
            <textarea name="full_description_fr" rows="7" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('full_description_fr', $service->full_description_fr) }}</textarea>
            <x-input-error :messages="$errors->get('full_description_fr')" class="mt-2" />
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
                    <option value="{{ $icon }}" @selected(old('icon', $service->icon ?: \App\Models\Service::DEFAULT_ICON) === $icon)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('icon')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Featured image path</span>
            <input name="featured_image" value="{{ old('featured_image', $service->featured_image) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('featured_image')" class="mt-2" />
        </label>
        <div class="md:col-span-2">
            <label class="flex min-h-11 items-center gap-3 rounded-md border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active)) class="rounded border-gray-300 text-nacho-primary focus:ring-nacho-primary">
                <span>Active on public website</span>
            </label>
        </div>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">SEO</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">SEO title EN</span>
            <input name="seo_title_en" value="{{ old('seo_title_en', $service->seo_title_en) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('seo_title_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">SEO title FR</span>
            <input name="seo_title_fr" value="{{ old('seo_title_fr', $service->seo_title_fr) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('seo_title_fr')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Meta description EN</span>
            <textarea name="meta_description_en" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('meta_description_en', $service->meta_description_en) }}</textarea>
            <x-input-error :messages="$errors->get('meta_description_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Meta description FR</span>
            <textarea name="meta_description_fr" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('meta_description_fr', $service->meta_description_fr) }}</textarea>
            <x-input-error :messages="$errors->get('meta_description_fr')" class="mt-2" />
        </label>
    </div>
</section>

<div class="flex items-center justify-end gap-3">
    <a href="{{ route('admin.services.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
        Cancel
    </a>
    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
        <x-lucide-save class="h-4 w-4" aria-hidden="true" />
        <span>{{ $submitLabel }}</span>
    </button>
</div>
