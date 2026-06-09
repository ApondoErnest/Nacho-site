@props([
    'title',
    'description',
    'href' => '#',
    'icon' => null,
])

<article {{ $attributes->class(['card-nacho flex flex-col p-6']) }}>
    @if ($icon)
        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-nacho-primary/10 text-nacho-primary" aria-hidden="true">
            <x-dynamic-component :component="'lucide-' . $icon" class="h-6 w-6" />
        </div>
    @endif
    <h3 class="text-lg font-bold text-nacho-dark">
        <a href="{{ $href }}" class="hover:text-nacho-primary">{{ $title }}</a>
    </h3>
    <p class="mt-2 flex-1 text-sm leading-relaxed text-nacho-dark/75">{{ $description }}</p>
    <div class="mt-5 flex flex-wrap gap-2">
        <a href="{{ $href }}" class="text-sm font-semibold text-nacho-primary hover:text-nacho-primary-dark">
            {{ __('components.service.learn_more') }} →
        </a>
        <a href="{{ route('book-inspection') }}" class="btn-nacho-secondary text-sm">{{ __('components.service.book_service') }}</a>
    </div>
</article>
