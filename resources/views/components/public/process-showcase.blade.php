@props([
    'image' => null,
])

@php
    $image = $image ?? 'images/technician-inside-vehicle.png';
    $imageUrl = file_exists(public_path($image)) ? asset($image) : null;
    $steps = __('home.process.showcase_steps');
    $icons = [
        'booking' => 'calendar-check',
        'vehicle' => 'car-front',
        'inspection' => 'scan-search',
        'report' => 'file-chart-column',
        'certificate' => 'badge-check',
    ];
@endphp

<section {{ $attributes->class(['process-showcase']) }}>
    @if ($imageUrl)
        <img src="{{ $imageUrl }}" alt="" class="process-showcase-image" loading="eager" />
    @endif
    <div class="process-showcase-overlay" aria-hidden="true"></div>

    <div class="process-showcase-content">
        <p class="process-showcase-kicker">{{ __('home.process.eyebrow') }}</p>
        <h2 class="process-showcase-title">{{ __('home.process.title') }}</h2>

        <ol class="process-showcase-steps" role="list">
            @foreach ($steps as $index => $step)
                <li class="process-showcase-step">
                    <span class="process-showcase-icon" aria-hidden="true">
                        <x-dynamic-component :component="'lucide-' . ($icons[$step['icon']] ?? $icons['booking'])" />
                    </span>

                    @if (! $loop->last)
                        <span class="process-showcase-arrow" aria-hidden="true">
                            <svg viewBox="0 0 90 24" fill="none">
                                <path d="M2 12h78m-10-9 10 9-10 9" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    @endif

                    <span class="process-showcase-number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="process-showcase-step-title">{{ $step['title'] }}</span>
                    <span class="process-showcase-step-text">{{ $step['text'] }}</span>
                </li>
            @endforeach
        </ol>
    </div>
</section>
