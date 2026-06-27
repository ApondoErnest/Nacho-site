<x-admin-layout title="Roles">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-500">Review fixed staff roles and their code-backed abilities</p>
                <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Role management</h2>
            </div>

            <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
                <x-lucide-users class="h-4 w-4" aria-hidden="true" />
                <span>Users</span>
            </a>
        </div>

        <section class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($roles as $role)
                @php
                    $abilities = $abilityMatrix[$role->slug] ?? [];
                @endphp
                <article class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="break-all text-xs font-bold uppercase text-nacho-primary">{{ $role->slug }}</p>
                            <h3 class="mt-1 break-words text-base font-bold text-gray-950">{{ $role->name }}</h3>
                            <p class="mt-2 break-words text-sm text-gray-600">{{ $role->description ?: 'No description set.' }}</p>
                        </div>
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                            <x-lucide-shield-check class="h-5 w-5" aria-hidden="true" />
                        </span>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-md border border-gray-200 bg-gray-50 p-3">
                            <p class="font-semibold text-gray-500">Users</p>
                            <p class="mt-1 text-xl font-bold text-gray-950">{{ number_format($role->users_count) }}</p>
                        </div>
                        <div class="rounded-md border border-gray-200 bg-gray-50 p-3">
                            <p class="font-semibold text-gray-500">Abilities</p>
                            <p class="mt-1 text-xl font-bold text-gray-950">{{ in_array('*', $abilities, true) ? 'All' : number_format(count($abilities)) }}</p>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap justify-end gap-2">
                        <a href="{{ route('admin.roles.show', $role) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                            <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                            <span>View</span>
                        </a>
                        @adminCan('roles.update')
                            <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                                <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                                <span>Edit</span>
                            </a>
                        @endadminCan
                    </div>
                </article>
            @endforeach
        </section>
    </div>
</x-admin-layout>
