@php
    $locale = app()->getLocale();
    $navItems = collect(config('navigation.main'));
    $navLinks = $navItems->reject(fn ($item) => $item['cta'] ?? false);
    $navCta = $navItems->first(fn ($item) => $item['cta'] ?? false);
    $centerMenuItems = collect(config('centers.centers', []))->map(function (array $center) use ($locale) {
        $suffix = $locale === 'fr' ? ($center['name_suffix_fr'] ?? '') : ($center['name_suffix_en'] ?? '');

        return [
            'label' => trim($center['name'] . ' ' . $suffix),
            'text' => $center['status'] === 'operational'
                ? trim(($center['city'] ?? '') . ' · ' . __('components.center.operational'))
                : trim(($center['city'] ?? '') . ' · ' . __('components.center.opening_badge')),
            'href' => route('centers.index') . '#' . $center['slug'],
            'status' => $center['status'],
        ];
    })->values();
    $serviceMenuItems = collect(config('home.services', []))->map(fn (array $service) => [
        'label' => __('home.services.' . $service['key'] . '.title'),
        'text' => __('home.services.' . $service['key'] . '.description'),
        'href' => route('services.index') . '#' . $service['slug'],
        'icon' => $service['icon'],
    ])->values();
@endphp

<header
    x-data="{ mobileOpen: false, scrolled: false }"
    @keydown.escape.window="mobileOpen = false"
    @scroll.window="scrolled = window.scrollY > 24"
    :class="scrolled ? 'nav-header--scrolled' : ''"
    class="nav-header"
>
    <div class="nav-utility-bar">
        <div class="nav-utility-inner">
            <p class="nav-utility-tagline">
                <span class="nav-utility-dot" aria-hidden="true"></span>
                <span>{{ __(config('navigation.utility_tagline_key')) }}</span>
            </p>

            <div class="nav-utility-cluster nav-utility-cluster--right">
                <a href="tel:{{ config('centers.headquarters.phone_primary_tel') }}" class="nav-utility-item">
                    <x-lucide-phone class="nav-utility-icon" aria-hidden="true" />
                    <span>{{ config('centers.headquarters.phone_primary') }}</span>
                </a>

                <a
                    href="mailto:{{ config('centers.headquarters.email') }}"
                    class="nav-utility-item nav-utility-item--email"
                    aria-label="{{ __('navigation.utility_email_label') }}: {{ config('centers.headquarters.email') }}"
                    title="{{ config('centers.headquarters.email') }}"
                >
                    <x-lucide-mail class="nav-utility-icon" aria-hidden="true" />
                    <span>{{ config('centers.headquarters.email') }}</span>
                </a>

                <span
                    class="nav-utility-item nav-utility-location"
                    aria-label="{{ __('navigation.utility_headquarters_label') }}: {{ config('centers.headquarters.address') }}"
                    title="{{ config('centers.headquarters.address') }}"
                >
                    <x-lucide-map-pin class="nav-utility-icon" aria-hidden="true" />
                    <span>{{ config('centers.headquarters.address') }}</span>
                </span>

                <span class="nav-utility-item">
                    <x-lucide-clock class="nav-utility-icon" aria-hidden="true" />
                    <span>{{ __(config('navigation.opening_hours_key')) }}</span>
                </span>

                <x-public.language-switcher variant="dark" class="nav-utility-language" />
            </div>
        </div>
    </div>

    <div class="nav-main-bar">
        <div class="nacho-container nav-main-inner">
            <a href="{{ route('home') }}" class="nav-brand" aria-label="{{ __('branding.logo_alt') }}">
                <x-nacho-logo context="nav" />
            </a>

            <nav class="nav-main-actions" aria-label="{{ __('navigation.main_navigation') }}">
                @foreach ($navLinks as $item)
                    @if ($item['route'] === 'centers.index' || $item['route'] === 'services.index')
                        @php
                            $dropdownItems = $item['route'] === 'centers.index' ? $centerMenuItems : $serviceMenuItems;
                            $columnSize = max(1, (int) ceil($dropdownItems->count() / 2));
                            $columns = $dropdownItems->chunk($columnSize);
                        @endphp

                        <div class="nav-dropdown">
                            <a
                                href="{{ route($item['route']) }}"
                                @class(['nav-link nav-link-dropdown', 'nav-link-active' => request()->routeIs($item['route'])])
                            >
                                <span>{{ __($item['label']) }}</span>
                                <x-lucide-chevron-down class="h-3.5 w-3.5" aria-hidden="true" />
                            </a>

                            <div class="nav-dropdown-panel">
                                <div class="nav-dropdown-grid">
                                    @foreach ($columns as $column)
                                        <div class="nav-dropdown-column">
                                            @foreach ($column as $dropdownItem)
                                                <a href="{{ $dropdownItem['href'] }}" class="nav-mega-item">
                                                    <span @class([
                                                        'nav-mega-icon',
                                                        'nav-mega-icon--status' => $item['route'] === 'centers.index',
                                                        'is-operational' => ($dropdownItem['status'] ?? null) === 'operational',
                                                    ])>
                                                        @if ($item['route'] === 'centers.index')
                                                            <x-lucide-building-2 aria-hidden="true" />
                                                        @else
                                                            <x-dynamic-component :component="'lucide-' . $dropdownItem['icon']" aria-hidden="true" />
                                                        @endif
                                                    </span>
                                                    <span>
                                                        <span class="nav-mega-title">{{ $dropdownItem['label'] }}</span>
                                                        <span class="nav-mega-text">{{ $dropdownItem['text'] }}</span>
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                <p class="nav-dropdown-footer">{{ __('navigation.dropdown_footer') }}</p>
                            </div>
                        </div>
                    @else
                        <a
                            href="{{ route($item['route']) }}"
                            @class(['nav-link', 'nav-link-active' => request()->routeIs($item['route'])])
                        >
                            {{ __($item['label']) }}
                        </a>
                    @endif
                @endforeach
            </nav>

            @if ($navCta)
                <a href="{{ route($navCta['route']) }}" class="nav-book-button">
                    <x-lucide-calendar-check class="h-5 w-5 shrink-0" aria-hidden="true" />
                    <span>{{ __($navCta['label']) }}</span>
                </a>
            @endif

            <button
                type="button"
                class="nav-mobile-toggle"
                @click="mobileOpen = true"
                aria-controls="mobile-navigation"
                :aria-expanded="mobileOpen.toString()"
            >
                <span class="sr-only">{{ __('navigation.menu_open') }}</span>
                <x-lucide-menu class="h-6 w-6" aria-hidden="true" />
            </button>
        </div>
    </div>

    <div
        x-show="mobileOpen"
        x-cloak
        class="fixed inset-0 z-[80] xl:hidden"
        role="dialog"
        aria-modal="true"
        id="mobile-navigation"
    >
        <div class="absolute inset-0 bg-nacho-dark/60" @click="mobileOpen = false" aria-hidden="true"></div>
        <div
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 flex w-full max-w-sm flex-col bg-white shadow-2xl"
        >
            <div class="flex items-center justify-between border-b border-nacho-dark/10 px-4 py-3">
                <x-nacho-logo context="sm" />
                <button type="button" class="rounded-lg p-2 text-nacho-dark hover:bg-nacho-cream" @click="mobileOpen = false">
                    <span class="sr-only">{{ __('navigation.menu_close') }}</span>
                    <x-lucide-x class="h-5 w-5" aria-hidden="true" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-4" aria-label="{{ __('navigation.main_navigation') }}">
                <ul class="space-y-1">
                    @foreach ($navLinks as $item)
                        <li>
                            <a
                                href="{{ route($item['route']) }}"
                                @class([
                                    'block rounded-lg px-3 py-3 text-base font-semibold',
                                    'bg-nacho-primary/10 text-nacho-primary' => request()->routeIs($item['route']),
                                    'text-nacho-dark hover:bg-nacho-cream' => ! request()->routeIs($item['route']),
                                ])
                                @click="mobileOpen = false"
                            >
                                {{ __($item['label']) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="space-y-4 border-t border-nacho-dark/10 p-4">
                @if ($navCta)
                    <a href="{{ route($navCta['route']) }}" class="nav-cta flex w-full justify-center" @click="mobileOpen = false">
                        {{ __($navCta['label']) }}
                    </a>
                @endif
                <a href="tel:{{ config('centers.headquarters.phone_primary_tel') }}" class="block text-center text-sm font-semibold text-nacho-dark">
                    {{ config('centers.headquarters.phone_primary') }}
                </a>
                <div class="flex justify-center">
                    <x-public.language-switcher variant="light" />
                </div>
            </div>
        </div>
    </div>
</header>
