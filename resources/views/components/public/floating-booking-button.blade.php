<div
    x-data="{
        show: false,
        update() {
            const about = document.querySelector('.about-nacho-section');
            const threshold = about ? about.offsetTop + about.offsetHeight : 900;
            this.show = window.scrollY > threshold;
        }
    }"
    x-init="update(); window.addEventListener('scroll', () => update(), { passive: true }); window.addEventListener('resize', () => update())"
    class="z-50"
>
    {{-- Desktop: right edge --}}
    <a
        href="{{ route('book-inspection') }}"
        x-show="show"
        x-cloak
        x-transition
        class="nav-cta fixed right-0 top-1/2 z-50 hidden -translate-y-1/2 gap-2 rounded-l-full rounded-r-none px-5 py-3 shadow-lg xl:inline-flex"
    >
        {{ __('navigation.book') }}
    </a>

    {{-- Mobile: fixed bottom (above content padding) --}}
    <div class="fixed inset-x-0 bottom-0 z-50 border-t border-nacho-dark/10 bg-white/95 p-3 shadow-[0_-4px_24px_rgba(42,39,36,0.12)] backdrop-blur-sm xl:hidden">
        <a href="{{ route('book-inspection') }}" class="nav-cta flex w-full justify-center">
            {{ __('navigation.book') }}
        </a>
    </div>
</div>
