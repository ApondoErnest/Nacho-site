<x-admin-layout title="Edit Career Department">
    <div class="space-y-5">
        <a href="{{ route('admin.career-departments.show', $department) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to department</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">{{ $department->slug }}</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Edit {{ $department->name_en }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.career-departments.update', $department) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.career-departments.partials.form', [
                'department' => $department,
                'submitLabel' => 'Save changes',
                'cancelUrl' => route('admin.career-departments.show', $department),
            ])
        </form>
    </div>
</x-admin-layout>
