@php
    $typeClasses = [
        'image' => 'bg-green-50 text-green-700',
        'document' => 'bg-blue-50 text-blue-700',
    ];
@endphp

<x-admin-layout title="Media Details">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <a href="{{ route('admin.media.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
                    <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
                    <span>Back to media</span>
                </a>
                <p class="mt-4 break-all text-sm font-semibold text-gray-500">{{ $media->file_path }}</p>
                <h2 class="mt-1 break-words text-2xl font-bold tracking-normal text-gray-950">{{ $media->file_name }}</h2>
            </div>

            <div class="flex flex-wrap gap-2">
                <span @class([
                    'inline-flex min-h-10 items-center rounded-full px-3 py-1.5 text-sm font-bold',
                    $typeClasses[$media->file_type] ?? 'bg-gray-100 text-gray-600',
                ])>
                    {{ str($media->file_type ?: 'file')->title() }}
                </span>
                <a href="{{ $media->publicUrl() }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
                    <x-lucide-external-link class="h-4 w-4" aria-hidden="true" />
                    <span>Open file</span>
                </a>
                @adminCan('media.update')
                    <a href="{{ route('admin.media.edit', $media) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                        <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                        <span>Edit</span>
                    </a>
                @endadminCan
                @adminCan('media.delete')
                    <form method="POST" action="{{ route('admin.media.destroy', $media) }}" onsubmit="return confirm('Delete this media file?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-red-200 bg-white px-4 py-2 text-sm font-bold text-red-700 hover:bg-red-50">
                            <x-lucide-trash-2 class="h-4 w-4" aria-hidden="true" />
                            <span>Delete</span>
                        </button>
                    </form>
                @endadminCan
            </div>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Type', 'value' => str($media->file_type ?: 'file')->title(), 'icon' => $media->isImage() ? 'image' : 'file-text'],
                ['label' => 'Size', 'value' => $media->humanFileSize(), 'icon' => 'hard-drive'],
                ['label' => 'Uploaded by', 'value' => $media->uploader?->name ?: 'Unknown', 'icon' => 'user'],
                ['label' => 'Uploaded', 'value' => $media->created_at?->format('Y-m-d H:i') ?: 'Not set', 'icon' => 'calendar-days'],
            ] as $summary)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-500">{{ $summary['label'] }}</p>
                            <p class="mt-2 break-words text-xl font-bold text-gray-950">{{ $summary['value'] }}</p>
                        </div>
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                            <x-dynamic-component :component="'lucide-' . $summary['icon']" class="h-5 w-5" aria-hidden="true" />
                        </span>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_26rem]">
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Preview</h3>
                </div>
                <div class="p-5">
                    @if ($media->isImage())
                        <div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-100">
                            <img src="{{ $media->publicUrl() }}" alt="{{ $media->alt_text_en ?: '' }}" class="max-h-[34rem] w-full object-contain">
                        </div>
                    @else
                        <div class="flex min-h-64 flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-gray-600">
                            <x-lucide-file-text class="h-12 w-12" aria-hidden="true" />
                            <p class="mt-3 text-sm font-bold text-gray-900">{{ $media->mime_type ?: 'Document file' }}</p>
                            <a href="{{ $media->publicUrl() }}" target="_blank" rel="noopener" class="mt-4 inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
                                <x-lucide-external-link class="h-4 w-4" aria-hidden="true" />
                                <span>Open document</span>
                            </a>
                        </div>
                    @endif
                </div>
            </section>

            <aside class="space-y-5">
                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-bold tracking-normal text-gray-950">Reuse Paths</h3>
                    </div>
                    <dl class="space-y-3 p-5 text-sm">
                        <div>
                            <dt class="font-semibold text-gray-500">Storage path</dt>
                            <dd class="mt-1 break-all font-mono text-xs text-gray-800">{{ $media->file_path }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Public URL</dt>
                            <dd class="mt-1 break-all font-mono text-xs text-gray-800">{{ $media->publicUrl() }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">MIME type</dt>
                            <dd class="mt-1 break-words text-gray-800">{{ $media->mime_type ?: 'Not set' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-bold tracking-normal text-gray-950">Alt Text</h3>
                    </div>
                    <dl class="space-y-3 p-5 text-sm">
                        <div>
                            <dt class="font-semibold text-gray-500">Alt text EN</dt>
                            <dd class="mt-1 break-words text-gray-800">{{ $media->alt_text_en ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Alt text FR</dt>
                            <dd class="mt-1 break-words text-gray-800">{{ $media->alt_text_fr ?: 'Not set' }}</dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
    </div>
</x-admin-layout>
