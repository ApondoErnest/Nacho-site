@props([
    'steps' => null,
])

@php
    $steps = $steps ?? __('components.process.steps');
@endphp

<ol {{ $attributes->class(['process-timeline']) }} role="list">
    @foreach ($steps as $index => $step)
        <li class="process-timeline-step">
            @if (! $loop->last)
                <span class="process-timeline-connector" aria-hidden="true"></span>
            @endif
            <div class="process-timeline-card">
                <div class="flex items-center gap-3">
                    <span class="process-timeline-number">{{ $index + 1 }}</span>
                    <span class="process-timeline-icon" aria-hidden="true">
                        <x-dynamic-component :component="'lucide-' . $step['icon']" class="h-5 w-5" />
                    </span>
                </div>
                <p class="mt-3 text-sm font-bold text-nacho-dark">{{ $step['title'] }}</p>
                <p class="mt-1 text-sm leading-relaxed text-nacho-dark/70">{{ $step['text'] }}</p>
            </div>
        </li>
    @endforeach
</ol>
