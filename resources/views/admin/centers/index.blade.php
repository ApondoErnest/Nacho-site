<x-admin-layout title="Centers">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-500">Manage NACHO inspection locations</p>
                <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Center management</h2>
            </div>

            @adminCan('centers.create')
                <a href="{{ route('admin.centers.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                    <x-lucide-plus class="h-4 w-4" aria-hidden="true" />
                    <span>Add center</span>
                </a>
            @endadminCan
        </div>

        <form method="GET" action="{{ route('admin.centers.index') }}" class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-[minmax(0,1fr)_14rem_auto]">
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Search</span>
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Name, city, or slug"
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary"
                >
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Status</span>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $statusOption)
                        <option value="{{ $statusOption->value }}" @selected($status === $statusOption->value)>
                            {{ str($statusOption->value)->replace('_', ' ')->title() }}
                        </option>
                    @endforeach
                </select>
            </label>

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-800 hover:bg-gray-50">
                    <x-lucide-search class="h-4 w-4" aria-hidden="true" />
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.centers.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-gray-200 px-3 py-2 text-sm font-bold text-gray-600 hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th scope="col" class="px-4 py-3">Center</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Services</th>
                            <th scope="col" class="px-4 py-3">Booking</th>
                            <th scope="col" class="px-4 py-3">Contacts</th>
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($centers as $center)
                            <tr class="align-top">
                                <td class="px-4 py-4">
                                    <div class="font-bold text-gray-950">{{ $center->name_en }}</div>
                                    <div class="mt-1 text-gray-500">{{ $center->city_en }} · {{ $center->slug }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold capitalize text-gray-700">
                                        {{ str($center->status->value)->replace('_', ' ') }}
                                    </span>
                                    @unless ($center->is_active)
                                        <span class="mt-2 inline-flex rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700">Inactive</span>
                                    @endunless
                                </td>
                                <td class="px-4 py-4 text-gray-700">{{ $center->services_count }}</td>
                                <td class="px-4 py-4">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                        $center->booking_enabled ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600',
                                    ])>
                                        {{ $center->booking_enabled ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-gray-700">{{ $center->contacts_count }} contacts</td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.centers.show', $center) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-950">
                                            <span class="sr-only">View {{ $center->name_en }}</span>
                                            <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                                        </a>
                                        @adminCan('centers.update')
                                            <a href="{{ route('admin.centers.edit', $center) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-950">
                                                <span class="sr-only">Edit {{ $center->name_en }}</span>
                                                <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                                            </a>
                                        @endadminCan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm font-semibold text-gray-500">
                                    No centers match the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $centers->links() }}
    </div>
</x-admin-layout>
