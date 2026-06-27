@php
    $statusClasses = [
        'active' => 'bg-green-50 text-green-700',
        'inactive' => 'bg-red-50 text-red-700',
    ];
@endphp

<x-admin-layout title="User Details">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
                    <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
                    <span>Back to users</span>
                </a>
                <p class="mt-4 break-all text-sm font-semibold text-gray-500">{{ $staffUser->email }}</p>
                <h2 class="mt-1 break-words text-2xl font-bold tracking-normal text-gray-950">{{ $staffUser->name }}</h2>
                <p class="mt-1 break-words text-sm text-gray-500">{{ $staffUser->role?->name ?: 'No role assigned' }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <span @class([
                    'inline-flex min-h-10 items-center rounded-full px-3 py-1.5 text-sm font-bold',
                    $statusClasses[$staffUser->status->value] ?? 'bg-gray-100 text-gray-600',
                ])>
                    {{ str($staffUser->status->value)->title() }}
                </span>
                @adminCan('users.update')
                    <a href="{{ route('admin.users.edit', $staffUser) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                        <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                        <span>Edit</span>
                    </a>
                @endadminCan
                @adminCan('users.delete')
                    @unless (auth()->id() === $staffUser->id)
                        <form method="POST" action="{{ route('admin.users.destroy', $staffUser) }}" onsubmit="return confirm('Deactivate this staff user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-red-200 bg-white px-4 py-2 text-sm font-bold text-red-700 hover:bg-red-50">
                                <x-lucide-user-x class="h-4 w-4" aria-hidden="true" />
                                <span>Deactivate</span>
                            </button>
                        </form>
                    @endunless
                @endadminCan
            </div>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Role', 'value' => $staffUser->role?->name ?: 'No role', 'icon' => 'shield-check'],
                ['label' => 'Status', 'value' => str($staffUser->status->value)->title(), 'icon' => $staffUser->isActive() ? 'user-check' : 'user-x'],
                ['label' => 'Last login', 'value' => $staffUser->last_login_at?->format('Y-m-d H:i') ?: 'Never', 'icon' => 'clock'],
                ['label' => 'Created', 'value' => $staffUser->created_at?->format('Y-m-d H:i') ?: 'Not set', 'icon' => 'calendar-days'],
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
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Contact and Account</h3>
                </div>
                <dl class="grid gap-5 p-5 text-sm md:grid-cols-2">
                    <div>
                        <dt class="font-semibold text-gray-500">Email</dt>
                        <dd class="mt-1 break-all text-gray-800">{{ $staffUser->email }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Phone</dt>
                        <dd class="mt-1 break-words text-gray-800">{{ $staffUser->phone ?: 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Email verified</dt>
                        <dd class="mt-1 text-gray-800">{{ $staffUser->email_verified_at?->format('Y-m-d H:i') ?: 'Not verified' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Updated</dt>
                        <dd class="mt-1 text-gray-800">{{ $staffUser->updated_at?->format('Y-m-d H:i') ?: 'Not set' }}</dd>
                    </div>
                </dl>
            </section>

            <aside class="space-y-5">
                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-bold tracking-normal text-gray-950">Role Summary</h3>
                    </div>
                    <dl class="space-y-3 p-5 text-sm">
                        <div>
                            <dt class="font-semibold text-gray-500">Role slug</dt>
                            <dd class="mt-1 break-all font-mono text-xs text-gray-800">{{ $staffUser->role?->slug ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Description</dt>
                            <dd class="mt-1 break-words text-gray-800">{{ $staffUser->role?->description ?: 'Not set' }}</dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
    </div>
</x-admin-layout>
