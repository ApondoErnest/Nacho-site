<x-admin-layout title="Edit Role">
    <div class="space-y-5">
        <a href="{{ route('admin.roles.show', $role) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to role</span>
        </a>

        <div>
            <p class="break-all text-sm font-semibold text-gray-500">{{ $role->slug }}</p>
            <h2 class="mt-1 break-words text-xl font-bold tracking-normal text-gray-950">Edit {{ $role->name }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.roles.partials.form', [
                'role' => $role,
                'submitLabel' => 'Save changes',
                'cancelUrl' => route('admin.roles.show', $role),
            ])
        </form>
    </div>
</x-admin-layout>
