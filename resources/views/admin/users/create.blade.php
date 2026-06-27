<x-admin-layout title="Add User">
    <div class="space-y-5">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to users</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">Create a staff account and assign an admin role</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Add staff user</h2>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf
            @include('admin.users.partials.form', [
                'staffUser' => $staffUser,
                'submitLabel' => 'Create user',
                'cancelUrl' => route('admin.users.index'),
                'passwordRequired' => true,
            ])
        </form>
    </div>
</x-admin-layout>
