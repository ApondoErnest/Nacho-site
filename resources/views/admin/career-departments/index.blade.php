<x-admin-layout title="Career Departments">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <a href="{{ route('admin.career-posts.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
                    <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
                    <span>Back to careers</span>
                </a>
                <p class="mt-4 text-sm font-semibold text-gray-500">Organize vacancy filters and career-area cards</p>
                <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Career departments</h2>
            </div>

            @adminCan('careers.create')
                <a href="{{ route('admin.career-departments.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                    <x-lucide-plus class="h-4 w-4" aria-hidden="true" />
                    <span>Add department</span>
                </a>
            @endadminCan
        </div>

        <form method="GET" action="{{ route('admin.career-departments.index') }}" class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-[minmax(0,1fr)_12rem_auto]">
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Search</span>
                <input type="search" name="search" value="{{ $search }}" placeholder="Name, slug, or description" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Status</span>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                    <option value="">All statuses</option>
                    <option value="active" @selected($status === 'active')>Active</option>
                    <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                </select>
            </label>

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-800 hover:bg-gray-50">
                    <x-lucide-search class="h-4 w-4" aria-hidden="true" />
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.career-departments.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-gray-200 px-3 py-2 text-sm font-bold text-gray-600 hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>

        <div class="grid gap-3 lg:hidden">
            @forelse ($departments as $department)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                            <x-dynamic-component :component="'lucide-' . $department->lucideIcon()" class="h-5 w-5" aria-hidden="true" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="break-words text-sm font-bold text-gray-950">{{ $department->name_en }}</h3>
                                    <p class="mt-1 break-all text-xs font-semibold text-gray-500">{{ $department->slug }}</p>
                                </div>
                                <span @class([
                                    'shrink-0 rounded-full px-2.5 py-1 text-xs font-bold',
                                    $department->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600',
                                ])>
                                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <dl class="mt-4 grid grid-cols-2 gap-2 text-xs">
                                <div class="rounded-md bg-gray-50 px-2 py-2">
                                    <dt class="font-semibold text-gray-500">Vacancies</dt>
                                    <dd class="mt-1 font-bold text-gray-950">{{ $department->posts_count }}</dd>
                                </div>
                                <div class="rounded-md bg-gray-50 px-2 py-2">
                                    <dt class="font-semibold text-gray-500">Order</dt>
                                    <dd class="mt-1 font-bold text-gray-950">{{ $department->display_order }}</dd>
                                </div>
                            </dl>

                            <div class="mt-4 flex flex-wrap justify-end gap-2">
                                <a href="{{ route('admin.career-departments.show', $department) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                                    <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                                    <span>View</span>
                                </a>
                                @adminCan('careers.update')
                                    <a href="{{ route('admin.career-departments.edit', $department) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                                        <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                                        <span>Edit</span>
                                    </a>
                                @endadminCan
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-10 text-center text-sm font-semibold text-gray-500 shadow-sm">
                    No career departments match the current filters.
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm lg:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th scope="col" class="px-4 py-3">Department</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Vacancies</th>
                            <th scope="col" class="px-4 py-3">Order</th>
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($departments as $department)
                            <tr class="align-top">
                                <td class="px-4 py-4">
                                    <div class="flex items-start gap-3">
                                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                                            <x-dynamic-component :component="'lucide-' . $department->lucideIcon()" class="h-5 w-5" aria-hidden="true" />
                                        </span>
                                        <div>
                                            <div class="font-bold text-gray-950">{{ $department->name_en }}</div>
                                            <div class="mt-1 break-all text-gray-500">{{ $department->slug }}</div>
                                            <div class="mt-1 text-xs text-gray-500">{{ $department->name_fr }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                        $department->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600',
                                    ])>
                                        {{ $department->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-gray-700">{{ $department->posts_count }}</td>
                                <td class="px-4 py-4 text-gray-700">{{ $department->display_order }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.career-departments.show', $department) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-950">
                                            <span class="sr-only">View {{ $department->name_en }}</span>
                                            <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                                        </a>
                                        @adminCan('careers.update')
                                            <a href="{{ route('admin.career-departments.edit', $department) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-950">
                                                <span class="sr-only">Edit {{ $department->name_en }}</span>
                                                <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                                            </a>
                                        @endadminCan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm font-semibold text-gray-500">
                                    No career departments match the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $departments->links() }}
    </div>
</x-admin-layout>
