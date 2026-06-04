@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $styles = match ($type) {
        'success' => ['border' => 'border-nacho-success/30', 'bg' => 'bg-nacho-success/10', 'text' => 'text-nacho-success', 'icon' => 'text-nacho-success'],
        'error' => ['border' => 'border-nacho-danger/30', 'bg' => 'bg-nacho-danger/10', 'text' => 'text-nacho-danger', 'icon' => 'text-nacho-danger'],
        'warning' => ['border' => 'border-nacho-warning/30', 'bg' => 'bg-nacho-warning/10', 'text' => 'text-nacho-warning', 'icon' => 'text-nacho-warning'],
        default => ['border' => 'border-nacho-primary/30', 'bg' => 'bg-nacho-primary/5', 'text' => 'text-nacho-primary', 'icon' => 'text-nacho-primary'],
    };
    $title = $title ?? __("components.alert.{$type}");
@endphp

<div
    {{ $attributes->class(['flex gap-3 rounded-xl border p-4', $styles['border'], $styles['bg']]) }}
    role="alert"
>
    <svg class="{{ 'mt-0.5 h-5 w-5 shrink-0 ' . $styles['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        @if ($type === 'success')
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        @elseif ($type === 'error')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        @else
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        @endif
    </svg>
    <div class="min-w-0 flex-1">
        <p class="{{ 'text-sm font-semibold ' . $styles['text'] }}">{{ $title }}</p>
        @if ($slot->isNotEmpty())
            <div class="mt-1 text-sm text-nacho-dark/80">{{ $slot }}</div>
        @endif
    </div>
</div>
