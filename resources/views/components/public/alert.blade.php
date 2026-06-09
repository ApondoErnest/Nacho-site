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
    $icon = match ($type) {
        'success' => 'circle-check',
        'error', 'warning' => 'circle-alert',
        default => 'info',
    };
    $title = $title ?? __("components.alert.{$type}");
@endphp

<div
    {{ $attributes->class(['flex gap-3 rounded-xl border p-4', $styles['border'], $styles['bg']]) }}
    role="alert"
>
    <x-dynamic-component :component="'lucide-' . $icon" class="{{ 'mt-0.5 h-5 w-5 shrink-0 ' . $styles['icon'] }}" aria-hidden="true" />
    <div class="min-w-0 flex-1">
        <p class="{{ 'text-sm font-semibold ' . $styles['text'] }}">{{ $title }}</p>
        @if ($slot->isNotEmpty())
            <div class="mt-1 text-sm text-nacho-dark/80">{{ $slot }}</div>
        @endif
    </div>
</div>
