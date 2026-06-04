<header
    x-data="{ mobileOpen: false }"
    @keydown.escape.window="mobileOpen = false"
    class="sticky top-0 z-40"
>
    {{-- Slim utility bar --}}
    <div class="nav-utility-bar">
        <div class="nacho-container flex h-9 items-center justify-between gap-3 text-xs sm:h-10 sm:text-sm">
            <div class="flex min-w-0 flex-1 items-center gap-4 sm:gap-6">
                <a
                    href="tel:{{ preg_replace('/\s+/', '', config('navigation.phone')) }}"
                    class="inline-flex items-center gap-1.5 truncate text-white/90 transition-colors hover:text-white"
                >
                    <svg class="h-3.5 w-3.5 shrink-0 text-nacho-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span class="truncate">{{ config('navigation.phone') }}</span>
                </a>
                <a
                    href="mailto:{{ config('navigation.email') }}"
                    class="hidden items-center gap-1.5 truncate text-white/90 transition-colors hover:text-white sm:inline-flex"
                >
                    <svg class="h-3.5 w-3.5 shrink-0 text-nacho-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    {{ config('navigation.email') }}
                </a>
            </div>
            <x-public.language-switcher variant="dark" class="shrink-0 text-xs sm:text-sm" />
        </div>
    </div>

    {{-- Main nav: logo left | menu on the right (config order: Book after Services) --}}
    <div class="nav-main-bar overflow-hidden">
        <div class="nav-main-inner">
            <a
                href="{{ route('home') }}"
                class="shrink-0 focus-visible:rounded-md"
                aria-label="{{ __('branding.logo_alt') }}"
            >
                <x-nacho-logo context="nav" />
            </a>

            <nav class="nav-main-actions" aria-label="{{ __('navigation.main_navigation') }}">
                @foreach (config('navigation.main') as $item)
                    @if ($item['cta'] ?? false)
                        <a href="{{ route($item['route']) }}" class="nav-cta ml-1">
                            {{ __($item['label']) }}
                        </a>
                    @else
                        <a
                            href="{{ route($item['route']) }}"
                            @class([
                                'nav-link',
                                'nav-link-active' => request()->routeIs($item['route']),
                            ])
                        >
                            {{ __($item['label']) }}
                        </a>
                    @endif
                @endforeach
            </nav>

            <button
                type="button"
                class="nav-mobile-toggle ml-auto inline-flex shrink-0 items-center justify-center rounded-lg border border-nacho-dark/10 p-2 text-nacho-dark transition-colors hover:border-nacho-primary/30 hover:bg-nacho-cream xl:hidden"
                @click="mobileOpen = ! mobileOpen"
                :aria-expanded="mobileOpen.toString()"
                aria-controls="mobile-navigation"
            >
                <span class="sr-only" x-text="mobileOpen ? '{{ __('navigation.menu_close') }}' : '{{ __('navigation.menu_open') }}'"></span>
                <svg x-show="! mobileOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileOpen" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div
        id="mobile-navigation"
        x-show="mobileOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="border-b border-nacho-dark/10 bg-white shadow-lg xl:hidden"
        @click.outside="mobileOpen = false"
    >
        <nav class="nacho-container max-h-[min(70vh,28rem)] overflow-y-auto py-3" aria-label="{{ __('navigation.main_navigation') }}">
            <div class="grid gap-0.5">
                @foreach (config('navigation.main') as $item)
                    @if ($item['cta'] ?? false)
                        <a
                            href="{{ route($item['route']) }}"
                            class="nav-cta mt-2 justify-center"
                            @click="mobileOpen = false"
                        >
                            {{ __($item['label']) }}
                        </a>
                    @else
                        <a
                            href="{{ route($item['route']) }}"
                            @class([
                                'rounded-lg px-3 py-2.5 text-base font-medium transition-colors',
                                'bg-nacho-primary/10 font-semibold text-nacho-primary' => request()->routeIs($item['route']),
                                'text-nacho-dark hover:bg-nacho-cream' => ! request()->routeIs($item['route']),
                            ])
                            @click="mobileOpen = false"
                        >
                            {{ __($item['label']) }}
                        </a>
                    @endif
                @endforeach
            </div>
            <div class="mt-3 flex items-center justify-between border-t border-nacho-dark/10 pt-3">
                <x-public.language-switcher variant="light" />
            </div>
        </nav>
    </div>
</header>
