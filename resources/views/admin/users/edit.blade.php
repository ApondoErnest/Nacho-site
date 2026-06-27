<x-admin-layout title="Edit User">
    <div class="space-y-5">
        <a href="{{ route('admin.users.show', $staffUser) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to user</span>
        </a>

        <div>
            <p class="break-all text-sm font-semibold text-gray-500">{{ $staffUser->email }}</p>
            <h2 class="mt-1 break-words text-xl font-bold tracking-normal text-gray-950">Edit {{ $staffUser->name }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $staffUser) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.users.partials.form', [
                'staffUser' => $staffUser,
                'submitLabel' => 'Save changes',
                'cancelUrl' => route('admin.users.show', $staffUser),
                'passwordRequired' => false,
            ])
        </form>
    </div>
</x-admin-layout>
