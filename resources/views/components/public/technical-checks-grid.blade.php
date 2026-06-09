@props([
    'checks' => null,
])

@php
    $checks = $checks ?? __('home.technical_checks.items');
    $icons = [
        'technical_inspection' => 'clipboard-check',
        'emission_testing' => 'spray-can',
        'brake_system' => 'disc-3',
        'engine_checks' => 'cog',
        'tires_more' => 'circle-dot',
        'side_slip' => 'car-front',
        'suspension_testing' => 'settings-2',
        'headlamp_testing' => 'lamp',
    ];
@endphp

<section {{ $attributes->class(['technical-check-band']) }}>
    <div class="technical-check-inner">
        <div class="technical-check-grid">
        @foreach ($checks as $check)
            @php
                $key = $check['key'] ?? 'technical_inspection';
            @endphp
            <article class="technical-check-card">
                <span class="technical-check-icon" aria-hidden="true">
                    <x-dynamic-component :component="'lucide-' . ($icons[$key] ?? $icons['technical_inspection'])" />
                </span>
                <h3 class="technical-check-title">{{ $check['title'] }}</h3>
                <p class="technical-check-text">{{ $check['text'] }}</p>
            </article>
        @endforeach
        </div>
    </div>
</section>
