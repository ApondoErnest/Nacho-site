@php
    $statusClasses = [
        'active' => 'bg-green-50 text-green-700',
        'inactive' => 'bg-red-50 text-red-700',
    ];

    $summaryCards = [
        ['label' => 'Total users', 'value' => $counts['total'], 'icon' => 'users'],
        ['label' => 'Active users', 'value' => $counts['active'], 'icon' => 'user-check'],
        ['label' => 'Inactive users', 'value' => $counts['inactive'], 'icon' => 'user-x'],
        ['label' => 'Super admins', 'value' => $counts['super_admin'], 'icon' => 'shield-check'],
    ];
@endphp

<x-admin-layout title="Users">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-500">Create staff accounts, assign roles, and manage access status</p>
                <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">User management</h2>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.roles.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
                    <x-lucide-shield-check class="h-4 w-4" aria-hidden="true" />
                    <span>Roles</span>
                </a>
                @adminCan('users.create')
                    <a href="{{ route('admin.users.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                        <x-lucide-user-plus class="h-4 w-4" aria-hidden="true" />
                        <span>Add user</span>
                    </a>
                @endadminCan
            </div>
        </div>

        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
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

        <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm lg:grid-cols-[minmax(0,1fr)_12rem_14rem_auto]">
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Search</span>
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Name, email, or phone"
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary"
                >
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Status</span>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $case)
                        <option value="{{ $case->value }}" @selected($status === $case->value)>{{ str($case->value)->title() }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Role</span>
                <select name="role_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                    <option value="">All roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) $roleId === (string) $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </label>

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-800 hover:bg-gray-50">
                    <x-lucide-search class="h-4 w-4" aria-hidden="true" />
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-gray-200 px-3 py-2 text-sm font-bold text-gray-600 hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>

        <div class="grid gap-3 lg:hidden">
            @forelse ($users as $staffUser)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="break-words text-sm font-bold text-gray-950">{{ $staffUser->name }}</h3>
                            <p class="mt-1 break-all text-xs font-semibold text-nacho-primary">{{ $staffUser->email }}</p>
                            <p class="mt-1 text-xs font-semibold text-gray-500">{{ $staffUser->role?->name ?: 'No role' }} · {{ $staffUser->last_login_at?->format('M j, Y H:i') ?: 'Never logged in' }}</p>
                        </div>
                        <span @class([
                            'shrink-0 rounded-full px-2.5 py-1 text-xs font-bold',
                            $statusClasses[$staffUser->status->value] ?? 'bg-gray-100 text-gray-600',
                        ])>
                            {{ str($staffUser->status->value)->title() }}
                        </span>
                    </div>

                    <div class="mt-4 flex flex-wrap justify-end gap-2">
                        <a href="{{ route('admin.users.show', $staffUser) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                            <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                            <span>View</span>
                        </a>
                        @adminCan('users.update')
                            <a href="{{ route('admin.users.edit', $staffUser) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                                <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                                <span>Edit</span>
                            </a>
                        @endadminCan
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-10 text-center text-sm font-semibold text-gray-500 shadow-sm">
                    No staff users match the current filters.
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm lg:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th scope="col" class="px-4 py-3">User</th>
                            <th scope="col" class="px-4 py-3">Role</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Last login</th>
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $staffUser)
                            <tr class="align-top">
                                <td class="max-w-md px-4 py-4">
                                    <div class="font-bold text-gray-950">{{ $staffUser->name }}</div>
                                    <div class="mt-1 break-all text-nacho-primary">{{ $staffUser->email }}</div>
                                    <div class="mt-1 text-xs text-gray-500">{{ $staffUser->phone ?: 'No phone' }}</div>
                                </td>
                                <td class="px-4 py-4 text-gray-700">{{ $staffUser->role?->name ?: 'No role' }}</td>
                                <td class="px-4 py-4">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                        $statusClasses[$staffUser->status->value] ?? 'bg-gray-100 text-gray-600',
                                    ])>
                                        {{ str($staffUser->status->value)->title() }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-gray-700">{{ $staffUser->last_login_at?->format('Y-m-d H:i') ?: 'Never' }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.users.show', $staffUser) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-950">
                                            <span class="sr-only">View {{ $staffUser->name }}</span>
                                            <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                                        </a>
                                        @adminCan('users.update')
                                            <a href="{{ route('admin.users.edit', $staffUser) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-950">
                                                <span class="sr-only">Edit {{ $staffUser->name }}</span>
                                                <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                                            </a>
                                        @endadminCan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm font-semibold text-gray-500">
                                    No staff users match the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $users->links() }}
    </div>
</x-admin-layout>
