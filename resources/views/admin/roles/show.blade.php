<x-admin-layout title="Role Details">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
                    <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
                    <span>Back to roles</span>
                </a>
                <p class="mt-4 break-all text-sm font-semibold text-gray-500">{{ $role->slug }}</p>
                <h2 class="mt-1 break-words text-2xl font-bold tracking-normal text-gray-950">{{ $role->name }}</h2>
                <p class="mt-1 break-words text-sm text-gray-500">{{ $role->description ?: 'No description set.' }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                @adminCan('roles.update')
                    <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                        <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                        <span>Edit</span>
                    </a>
                @endadminCan
            </div>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ([
                ['label' => 'Users assigned', 'value' => number_format($role->users_count), 'icon' => 'users'],
                ['label' => 'Abilities', 'value' => in_array('*', $abilities, true) ? 'All' : number_format(count($abilities)), 'icon' => 'shield-check'],
                ['label' => 'Updated', 'value' => $role->updated_at?->format('Y-m-d H:i') ?: 'Not set', 'icon' => 'calendar-clock'],
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

        <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-5 py-4">
                <h3 class="text-base font-bold tracking-normal text-gray-950">Abilities</h3>
            </div>
            <div class="p-5">
                @if (in_array('*', $abilities, true))
                    <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-bold text-green-800">
                        This role has access to every admin ability.
                    </div>
                @else
                    <div class="flex flex-wrap gap-2">
                        @forelse ($abilities as $ability)
                            <span class="inline-flex rounded-full bg-gray-100 px-3 py-1.5 font-mono text-xs font-bold text-gray-700">{{ $ability }}</span>
                        @empty
                            <p class="text-sm font-semibold text-gray-500">No abilities are mapped to this role.</p>
                        @endforelse
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-admin-layout>
