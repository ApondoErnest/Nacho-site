@props([
    'paginator' => null,
])

@if ($paginator)
    {{ $paginator->links('vendor.pagination.nacho') }}
@else
    <nav role="navigation" aria-label="{{ __('components.pagination.navigation') }}" class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
        <p class="text-sm text-nacho-dark/70">
            {{ __('components.pagination.showing', ['from' => 1, 'to' => 10, 'total' => 42]) }}
        </p>
        <div class="flex flex-wrap items-center gap-1">
            <span class="inline-flex cursor-not-allowed items-center rounded-lg border border-nacho-dark/10 px-3 py-2 text-sm font-medium text-nacho-dark/40">
                {{ __('components.pagination.previous') }}
            </span>
            <span aria-current="page" class="inline-flex min-w-[2.5rem] items-center justify-center rounded-lg bg-nacho-primary px-3 py-2 text-sm font-semibold text-white">1</span>
            <a href="#" class="inline-flex min-w-[2.5rem] items-center justify-center rounded-lg border border-nacho-dark/15 bg-white px-3 py-2 text-sm font-medium text-nacho-dark hover:border-nacho-primary hover:text-nacho-primary">2</a>
            <a href="#" class="inline-flex min-w-[2.5rem] items-center justify-center rounded-lg border border-nacho-dark/15 bg-white px-3 py-2 text-sm font-medium text-nacho-dark hover:border-nacho-primary hover:text-nacho-primary">3</a>
            <a href="#" class="inline-flex items-center rounded-lg border border-nacho-dark/15 bg-white px-3 py-2 text-sm font-semibold text-nacho-dark hover:border-nacho-primary hover:text-nacho-primary">
                {{ __('components.pagination.next') }}
            </a>
        </div>
    </nav>
@endif
