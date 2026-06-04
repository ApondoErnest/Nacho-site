@props([
    'title',
    'excerpt',
    'href' => '#',
    'category' => null,
    'date' => null,
    'imageUrl' => null,
])

<article {{ $attributes->class(['card-nacho overflow-hidden']) }}>
    <div class="aspect-[16/9] bg-nacho-cream">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="" class="h-full w-full object-cover" loading="lazy" />
        @endif
    </div>
    <div class="p-5">
        @if ($category || $date)
            <div class="flex flex-wrap items-center gap-2 text-xs font-medium uppercase tracking-wide text-nacho-dark/50">
                @if ($category)<span class="text-nacho-primary">{{ $category }}</span>@endif
                @if ($category && $date)<span aria-hidden="true">·</span>@endif
                @if ($date)<time>{{ $date }}</time>@endif
            </div>
        @endif
        <h3 class="mt-2 text-lg font-bold text-nacho-dark">
            <a href="{{ $href }}" class="hover:text-nacho-primary">{{ $title }}</a>
        </h3>
        <p class="mt-2 text-sm leading-relaxed text-nacho-dark/75">{{ $excerpt }}</p>
        <a href="{{ $href }}" class="mt-4 inline-block text-sm font-semibold text-nacho-primary hover:text-nacho-primary-dark">
            {{ __('components.blog.read_more') }} →
        </a>
    </div>
</article>
