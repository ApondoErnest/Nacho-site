@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
        @if ($paginator->total())
            <p class="text-sm text-nacho-dark/70">
                {{ __('components.pagination.showing', [
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                    'total' => $paginator->total(),
                ]) }}
            </p>
        @endif

        <div class="flex flex-wrap items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex cursor-not-allowed items-center rounded-lg border border-nacho-dark/10 px-3 py-2 text-sm font-medium text-nacho-dark/40">
                    {{ __('components.pagination.previous') }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center rounded-lg border border-nacho-dark/15 bg-white px-3 py-2 text-sm font-semibold text-nacho-dark transition-colors hover:border-nacho-primary hover:text-nacho-primary">
                    {{ __('components.pagination.previous') }}
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-nacho-dark/40">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex min-w-[2.5rem] items-center justify-center rounded-lg bg-nacho-primary px-3 py-2 text-sm font-semibold text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="inline-flex min-w-[2.5rem] items-center justify-center rounded-lg border border-nacho-dark/15 bg-white px-3 py-2 text-sm font-medium text-nacho-dark transition-colors hover:border-nacho-primary hover:text-nacho-primary">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center rounded-lg border border-nacho-dark/15 bg-white px-3 py-2 text-sm font-semibold text-nacho-dark transition-colors hover:border-nacho-primary hover:text-nacho-primary">
                    {{ __('components.pagination.next') }}
                </a>
            @else
                <span class="inline-flex cursor-not-allowed items-center rounded-lg border border-nacho-dark/10 px-3 py-2 text-sm font-medium text-nacho-dark/40">
                    {{ __('components.pagination.next') }}
                </span>
            @endif
        </div>
    </nav>
@endif
