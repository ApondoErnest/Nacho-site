<x-admin-layout title="Add Career Department">
    <div class="space-y-5">
        <a href="{{ route('admin.career-departments.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
            <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
            <span>Back to departments</span>
        </a>

        <div>
            <p class="text-sm font-semibold text-gray-500">Create a career-area filter for vacancies</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Add career department</h2>
        </div>

        <form method="POST" action="{{ route('admin.career-departments.store') }}" class="space-y-5">
            @csrf
            @include('admin.career-departments.partials.form', [
                'department' => $department,
                'submitLabel' => 'Create department',
                'cancelUrl' => route('admin.career-departments.index'),
            ])
        </form>
    </div>
</x-admin-layout>
