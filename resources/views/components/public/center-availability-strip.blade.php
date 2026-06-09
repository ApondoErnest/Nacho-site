<section {{ $attributes->class(['rounded-xl bg-white px-4 py-4 shadow-sm ring-1 ring-nacho-dark/10 sm:px-6']) }} aria-label="{{ __('home.availability.title') }}">
    <div class="flex flex-col items-center justify-center gap-3 text-center sm:flex-row sm:flex-wrap sm:gap-6">
        <span class="inline-flex items-center gap-2 text-sm font-semibold text-nacho-success sm:text-base">
            <span class="h-2.5 w-2.5 rounded-full bg-nacho-success" aria-hidden="true"></span>
            {{ __('home.availability.operational') }}
        </span>
        <span class="hidden text-nacho-dark/30 sm:inline" aria-hidden="true">|</span>
        <span class="inline-flex items-center gap-2 text-sm font-semibold text-nacho-warning sm:text-base">
            <span class="h-2.5 w-2.5 rounded-full bg-nacho-warning" aria-hidden="true"></span>
            {{ __('home.availability.construction') }}
        </span>
        <span class="hidden text-nacho-dark/30 sm:inline" aria-hidden="true">|</span>
        <span class="text-sm font-medium text-nacho-dark/75 sm:text-base">{{ __('home.availability.opening') }}</span>
    </div>
</section>
