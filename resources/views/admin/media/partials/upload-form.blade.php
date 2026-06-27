<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">File</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-[minmax(0,1fr)_18rem]">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Upload file</span>
            <input
                type="file"
                name="file"
                required
                accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,image/jpeg,image/png,image/webp,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                class="mt-1 block w-full rounded-md border border-gray-300 text-sm text-gray-700 file:mr-4 file:border-0 file:bg-nacho-primary file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-white hover:file:bg-nacho-primary-dark focus:border-nacho-primary focus:ring-nacho-primary"
            >
            <x-input-error :messages="$errors->get('file')" class="mt-2" />
        </label>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
            <p class="font-bold text-gray-800">Allowed uploads</p>
            <p class="mt-2">Images: JPG, PNG, WebP</p>
            <p>Documents: PDF, DOC, DOCX</p>
            <p class="mt-2">Maximum size: 10 MB</p>
        </div>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Accessibility Text</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Alt text EN</span>
            <input name="alt_text_en" value="{{ old('alt_text_en') }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('alt_text_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Alt text FR</span>
            <input name="alt_text_fr" value="{{ old('alt_text_fr') }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('alt_text_fr')" class="mt-2" />
        </label>
    </div>
</section>

<div class="flex items-center justify-end gap-3">
    <a href="{{ $cancelUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
        Cancel
    </a>
    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
        <x-lucide-upload class="h-4 w-4" aria-hidden="true" />
        <span>{{ $submitLabel }}</span>
    </button>
</div>
