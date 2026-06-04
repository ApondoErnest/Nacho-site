@props([
    'title',
    'location',
    'department' => null,
    'employmentType' => null,
    'deadline' => null,
    'href' => '#',
])

<article {{ $attributes->class(['card-nacho flex flex-col p-6']) }}>
    <h3 class="text-lg font-bold text-nacho-dark">
        <a href="{{ $href }}" class="hover:text-nacho-primary">{{ $title }}</a>
    </h3>
    <p class="mt-1 text-sm font-medium text-nacho-primary">{{ $location }}</p>

    <dl class="mt-4 space-y-2 text-sm text-nacho-dark/75">
        @if ($department)
            <div class="flex gap-2">
                <dt class="font-semibold text-nacho-dark">{{ __('components.career.department') }}:</dt>
                <dd>{{ $department }}</dd>
            </div>
        @endif
        @if ($employmentType)
            <div class="flex gap-2">
                <dt class="font-semibold text-nacho-dark">{{ __('components.career.employment_type') }}:</dt>
                <dd>{{ $employmentType }}</dd>
            </div>
        @endif
        @if ($deadline)
            <div class="flex gap-2">
                <dt class="font-semibold text-nacho-dark">{{ __('components.career.deadline') }}:</dt>
                <dd>{{ $deadline }}</dd>
            </div>
        @endif
    </dl>

    <a href="{{ $href }}" class="btn-nacho-primary mt-5 inline-flex w-full justify-center text-sm sm:w-auto">
        {{ __('components.career.apply') }}
    </a>
</article>
