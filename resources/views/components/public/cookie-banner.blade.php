<div
    x-data="{ visible: ! localStorage.getItem('nacho_cookie_consent') }"
    x-show="visible"
    x-cloak
    class="fixed inset-x-0 bottom-0 z-50 border-t border-nacho-dark/10 bg-white p-4 shadow-lg sm:p-5"
    role="dialog"
    aria-label="Cookie consent"
>
    <div class="nacho-container flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-nacho-dark/80">
            {{ __('footer.cookie_message') }}
            <a href="{{ route('legal.cookies') }}" class="font-medium text-nacho-primary underline underline-offset-2 hover:text-nacho-primary-dark">
                {{ __('footer.cookie_learn_more') }}
            </a>
        </p>
        <button
            type="button"
            class="btn-nacho-primary shrink-0"
            @click="localStorage.setItem('nacho_cookie_consent', '1'); visible = false"
        >
            {{ __('footer.cookie_accept') }}
        </button>
    </div>
</div>
