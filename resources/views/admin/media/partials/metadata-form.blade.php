<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Details</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block md:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Display filename</span>
            <input name="file_name" value="{{ old('file_name', $media->file_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('file_name')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Alt text EN</span>
            <input name="alt_text_en" value="{{ old('alt_text_en', $media->alt_text_en) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('alt_text_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Alt text FR</span>
            <input name="alt_text_fr" value="{{ old('alt_text_fr', $media->alt_text_fr) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('alt_text_fr')" class="mt-2" />
        </label>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Stored File</h3>
    </div>
    <dl class="grid gap-4 p-5 text-sm md:grid-cols-2">
        <div>
            <dt class="font-semibold text-gray-500">Storage path</dt>
            <dd class="mt-1 break-all font-mono text-xs text-gray-800">{{ $media->file_path }}</dd>
        </div>
        <div>
            <dt class="font-semibold text-gray-500">Public URL</dt>
            <dd class="mt-1 break-all font-mono text-xs text-gray-800">{{ $media->publicUrl() }}</dd>
        </div>
    </dl>
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
