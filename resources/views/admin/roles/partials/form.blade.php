<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Role Details</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Name</span>
            <input name="name" value="{{ old('name', $role->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Slug</span>
            <input name="slug" value="{{ old('slug', $role->slug) }}" readonly class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 font-mono text-sm text-gray-600 shadow-sm">
            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
        </label>
        <label class="block md:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Description</span>
            <textarea name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('description', $role->description) }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </label>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Mapped Abilities</h3>
    </div>
    <div class="p-5">
        @if (in_array('*', $abilities, true))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-bold text-green-800">
                This role has access to every admin ability.
            </div>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($abilities as $ability)
                    <span class="inline-flex rounded-full bg-gray-100 px-3 py-1.5 font-mono text-xs font-bold text-gray-700">{{ $ability }}</span>
                @endforeach
            </div>
        @endif
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
