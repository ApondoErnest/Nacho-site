<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-nacho-primary">NACHO Admin</p>
                <h1 class="text-2xl font-semibold text-gray-900">Access foundation</h1>
            </div>
            <p class="text-sm text-gray-500">{{ Auth::user()->role?->name }}</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">Step 26</p>
                    <h2 class="mt-2 text-xl font-semibold text-gray-950">Admin access is active</h2>
                    <p class="mt-3 text-sm leading-6 text-gray-600">
                        This protected entry point confirms authentication, active staff status, and custom role abilities.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
