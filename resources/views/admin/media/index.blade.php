@php
    $typeClasses = [
        'image' => 'bg-green-50 text-green-700',
        'document' => 'bg-blue-50 text-blue-700',
    ];

    $summaryCards = [
        ['label' => 'Total files', 'value' => $counts['total'], 'icon' => 'library'],
        ['label' => 'Images', 'value' => $counts['image'], 'icon' => 'image'],
        ['label' => 'Documents', 'value' => $counts['document'], 'icon' => 'file-text'],
    ];
@endphp

<x-admin-layout title="Media">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-500">Upload and manage reusable public-site files</p>
                <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Media library</h2>
            </div>

            @adminCan('media.create')
                <a href="{{ route('admin.media.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                    <x-lucide-upload class="h-4 w-4" aria-hidden="true" />
                    <span>Upload media</span>
                </a>
            @endadminCan
        </div>

        <section class="grid gap-3 sm:grid-cols-3">
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

        <form method="GET" action="{{ route('admin.media.index') }}" class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm lg:grid-cols-[minmax(0,1fr)_12rem_auto]">
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Search</span>
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Filename, path, mime, or alt text"
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary"
                >
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Type</span>
                <select name="type" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                    <option value="">All types</option>
                    @foreach ($types as $value => $label)
                        <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-800 hover:bg-gray-50">
                    <x-lucide-search class="h-4 w-4" aria-hidden="true" />
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.media.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-gray-200 px-3 py-2 text-sm font-bold text-gray-600 hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>

        <div class="grid gap-3 lg:hidden">
            @forelse ($mediaItems as $media)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-md bg-gray-100 text-gray-500">
                            @if ($media->isImage())
                                <img src="{{ $media->publicUrl() }}" alt="{{ $media->alt_text_en ?: '' }}" class="h-full w-full object-cover" loading="lazy">
                            @else
                                <x-lucide-file-text class="h-7 w-7" aria-hidden="true" />
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="break-words text-sm font-bold text-gray-950">{{ $media->file_name }}</h3>
                                <span @class([
                                    'shrink-0 rounded-full px-2.5 py-1 text-xs font-bold',
                                    $typeClasses[$media->file_type] ?? 'bg-gray-100 text-gray-600',
                                ])>
                                    {{ str($media->file_type ?: 'file')->title() }}
                                </span>
                            </div>
                            <p class="mt-1 break-all text-xs font-semibold text-nacho-primary">{{ $media->file_path }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $media->humanFileSize() }} · {{ $media->uploader?->name ?: 'Unknown uploader' }}</p>
                        </div>
                    </div>

                    <p class="mt-3 break-words text-sm text-gray-700">{{ $media->alt_text_en ?: 'No English alt text yet.' }}</p>

                    <div class="mt-4 flex flex-wrap justify-end gap-2">
                        <a href="{{ route('admin.media.show', $media) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                            <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                            <span>View</span>
                        </a>
                        @adminCan('media.update')
                            <a href="{{ route('admin.media.edit', $media) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                                <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                                <span>Edit</span>
                            </a>
                        @endadminCan
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-10 text-center text-sm font-semibold text-gray-500 shadow-sm">
                    No media files match the current filters.
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm lg:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th scope="col" class="px-4 py-3">File</th>
                            <th scope="col" class="px-4 py-3">Type</th>
                            <th scope="col" class="px-4 py-3">Size</th>
                            <th scope="col" class="px-4 py-3">Uploaded by</th>
                            <th scope="col" class="px-4 py-3">Uploaded</th>
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($mediaItems as $media)
                            <tr class="align-top">
                                <td class="max-w-md px-4 py-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-md bg-gray-100 text-gray-500">
                                            @if ($media->isImage())
                                                <img src="{{ $media->publicUrl() }}" alt="{{ $media->alt_text_en ?: '' }}" class="h-full w-full object-cover" loading="lazy">
                                            @else
                                                <x-lucide-file-text class="h-6 w-6" aria-hidden="true" />
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="break-words font-bold text-gray-950">{{ $media->file_name }}</div>
                                            <div class="mt-1 break-all text-xs text-nacho-primary">{{ $media->file_path }}</div>
                                            <div class="mt-1 break-words text-xs text-gray-500">{{ $media->alt_text_en ?: 'No English alt text yet.' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                        $typeClasses[$media->file_type] ?? 'bg-gray-100 text-gray-600',
                                    ])>
                                        {{ str($media->file_type ?: 'file')->title() }}
                                    </span>
                                    <div class="mt-1 break-words text-xs text-gray-500">{{ $media->mime_type ?: 'Unknown MIME' }}</div>
                                </td>
                                <td class="px-4 py-4 text-gray-700">{{ $media->humanFileSize() }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $media->uploader?->name ?: 'Unknown' }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $media->created_at?->format('Y-m-d H:i') ?: 'Not set' }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.media.show', $media) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-950">
                                            <span class="sr-only">View {{ $media->file_name }}</span>
                                            <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                                        </a>
                                        @adminCan('media.update')
                                            <a href="{{ route('admin.media.edit', $media) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-950">
                                                <span class="sr-only">Edit {{ $media->file_name }}</span>
                                                <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                                            </a>
                                        @endadminCan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm font-semibold text-gray-500">
                                    No media files match the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $mediaItems->links() }}
    </div>
</x-admin-layout>
