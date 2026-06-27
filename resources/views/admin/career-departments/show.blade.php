<x-admin-layout title="Career Department Details">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <a href="{{ route('admin.career-departments.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
                    <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
                    <span>Back to departments</span>
                </a>
                <div class="mt-4 flex items-center gap-3">
                    <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                        <x-dynamic-component :component="'lucide-' . $department->lucideIcon()" class="h-6 w-6" aria-hidden="true" />
                    </span>
                    <div>
                        <p class="break-all text-sm font-semibold text-gray-500">{{ $department->slug }}</p>
                        <h2 class="mt-1 break-words text-2xl font-bold tracking-normal text-gray-950">{{ $department->name_en }}</h2>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                @adminCan('careers.update')
                    <a href="{{ route('admin.career-departments.edit', $department) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                        <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                        <span>Edit</span>
                    </a>
                @endadminCan
                @adminCan('careers.delete')
                    <form method="POST" action="{{ route('admin.career-departments.destroy', $department) }}" onsubmit="return confirm('Deactivate this career department?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-red-200 bg-white px-4 py-2 text-sm font-bold text-red-700 hover:bg-red-50">
                            <x-lucide-ban class="h-4 w-4" aria-hidden="true" />
                            <span>Deactivate</span>
                        </button>
                    </form>
                @endadminCan
            </div>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Status', 'value' => $department->is_active ? 'Active' : 'Inactive', 'icon' => 'activity'],
                ['label' => 'Display order', 'value' => $department->display_order, 'icon' => 'arrow-down-up'],
                ['label' => 'Vacancies', 'value' => $department->posts_count, 'icon' => 'briefcase-business'],
                ['label' => 'Updated', 'value' => $department->updated_at?->format('Y-m-d H:i') ?: 'Not set', 'icon' => 'calendar-days'],
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
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Description</h3>
                </div>
                <dl class="grid gap-5 p-5 text-sm md:grid-cols-2">
                    <div>
                        <dt class="font-semibold text-gray-500">Name FR</dt>
                        <dd class="mt-1 break-words font-bold text-gray-950">{{ $department->name_fr }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Icon</dt>
                        <dd class="mt-1 break-words text-gray-800">{{ $department->icon ?: 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Description EN</dt>
                        <dd class="mt-1 whitespace-pre-line break-words text-gray-800">{{ $department->description_en ?: 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Description FR</dt>
                        <dd class="mt-1 whitespace-pre-line break-words text-gray-800">{{ $department->description_fr ?: 'Not set' }}</dd>
                    </div>
                </dl>
            </section>

            <aside class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Vacancies in department</h3>
                </div>
                <div class="space-y-2 p-5">
                    @forelse ($department->posts as $post)
                        <a href="{{ route('admin.career-posts.show', $post) }}" class="block rounded-md border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50">
                            <span class="block font-bold text-gray-950">{{ $post->title_en }}</span>
                            <span class="mt-1 block text-xs font-semibold text-gray-500">{{ $post->reference }} · {{ str($post->status->value)->replace('_', ' ')->title() }} · {{ $post->center?->name_en ?: 'All centers' }}</span>
                        </a>
                    @empty
                        <p class="text-sm font-semibold text-gray-500">No vacancies use this department yet.</p>
                    @endforelse
                </div>
            </aside>
        </div>
    </div>
</x-admin-layout>
