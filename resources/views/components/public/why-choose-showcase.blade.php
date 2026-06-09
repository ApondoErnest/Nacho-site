@php
    $items = __('home.why.showcase');
    $icons = [
        'shield-check' => 'shield-check',
        'technician' => 'user-check',
        'shield-alert' => 'shield-alert',
        'price-tag' => 'tag',
        'map-pin' => 'map-pin',
    ];
@endphp

<section {{ $attributes->class(['why-choose-showcase']) }}>
    <h2 class="why-choose-title">{{ __('home.why.title') }}</h2>

    <div class="why-choose-grid">
        @foreach ($items as $item)
            <article class="why-choose-card why-choose-card--{{ $item['tone'] }}">
                <span class="why-choose-icon" aria-hidden="true">
                    <x-dynamic-component :component="'lucide-' . ($icons[$item['icon']] ?? 'circle-check')" />
                </span>

                <span class="why-choose-copy">
                    <span class="why-choose-card-title">{{ $item['title'] }}</span>
                    <span class="why-choose-card-text">{{ $item['text'] }}</span>
                </span>
            </article>
        @endforeach
    </div>
</section>
