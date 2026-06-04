<footer class="relative bg-nacho-dark text-white">
    {{-- Accent strip --}}
    <div class="h-1 bg-gradient-to-r from-nacho-primary-dark via-nacho-primary to-nacho-primary-dark"></div>

    <div class="nacho-container py-10 sm:py-12">
        {{-- CTA band --}}
        <div class="mb-10 flex flex-col items-start justify-between gap-4 rounded-2xl border border-white/10 bg-white/5 p-6 sm:flex-row sm:items-center sm:p-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-nacho-primary">
                    {{ __('footer.cta_eyebrow') }}
                </p>
                <p class="mt-1 text-lg font-semibold text-white sm:text-xl">
                    {{ __('footer.cta_title') }}
                </p>
            </div>
            <a href="{{ route('book-inspection') }}" class="nav-cta shrink-0">
                {{ __('navigation.book') }}
            </a>
        </div>

        {{-- Logo | Quick links | Contact | Legal — single horizontal row (desktop/tablet) --}}
        <div class="flex flex-col gap-10 sm:flex-row sm:flex-nowrap sm:items-start sm:justify-between sm:gap-x-6 lg:gap-x-10">
            <section class="min-w-0 shrink-0 sm:w-[28%] lg:w-[26%]">
                <a href="{{ route('home') }}" class="inline-block focus-visible:rounded-md" aria-label="{{ __('branding.logo_alt') }}">
                    <x-nacho-logo context="footer" />
                </a>
                <p class="footer-tagline mt-4">
                    {{ __('footer.tagline') }}
                </p>
            </section>

            <section class="min-w-0 sm:w-[22%] lg:w-[24%]">
                <h2 class="footer-heading">{{ __('footer.quick_links') }}</h2>
                <ul class="mt-4 space-y-2">
                    @foreach (config('navigation.main') as $item)
                        @unless ($item['cta'] ?? false)
                            <li>
                                <a href="{{ route($item['route']) }}" class="footer-link">
                                    {{ __($item['label']) }}
                                </a>
                            </li>
                        @endunless
                    @endforeach
                    <li>
                        <a href="{{ route('book-inspection') }}" class="footer-link font-medium text-nacho-primary hover:text-white">
                            {{ __('navigation.book') }}
                        </a>
                    </li>
                </ul>
            </section>

            <section class="min-w-0 sm:w-[22%] lg:w-[24%]">
                <h2 class="footer-heading">{{ __('footer.contact') }}</h2>
                <ul class="mt-4 space-y-2.5">
                    <li>
                        <a href="tel:{{ preg_replace('/\s+/', '', config('navigation.phone')) }}" class="footer-link inline-flex items-start gap-2">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-nacho-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            {{ config('navigation.phone') }}
                        </a>
                    </li>
                    <li>
                        <a href="mailto:{{ config('navigation.email') }}" class="footer-link inline-flex items-start gap-2">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-nacho-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ config('navigation.email') }}
                        </a>
                    </li>
                    <li class="footer-link inline-flex items-start gap-2">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-nacho-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ config('navigation.address') }}
                    </li>
                </ul>
            </section>

            <section class="min-w-0 sm:w-[22%] lg:w-[22%]">
                <h2 class="footer-heading">{{ __('footer.legal') }}</h2>
                <ul class="mt-4 space-y-2">
                    @foreach (config('navigation.legal') as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" class="footer-link">
                                {{ __($item['label']) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-white/10 bg-black/20">
        <div class="nacho-container flex flex-col items-center justify-between gap-3 py-5 text-center text-sm text-white/55 sm:flex-row sm:text-left">
            <p>{{ __('footer.copyright', ['year' => date('Y')]) }}</p>
            <p class="text-xs text-white/45">{{ __('footer.trust_line') }}</p>
        </div>
    </div>
</footer>
