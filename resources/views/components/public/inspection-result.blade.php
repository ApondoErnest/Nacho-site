@props([
    'type' => 'accepted',
])

@php
    $config = match ($type) {
        'suspended' => [
            'border' => 'border-nacho-warning/30',
            'bg' => 'bg-nacho-warning/10',
            'title' => 'text-nacho-warning',
            'titleKey' => 'components.results.suspended_title',
            'textKey' => 'components.results.suspended_text',
        ],
        'refused' => [
            'border' => 'border-nacho-danger/30',
            'bg' => 'bg-nacho-danger/10',
            'title' => 'text-nacho-danger',
            'titleKey' => 'components.results.refused_title',
            'textKey' => 'components.results.refused_text',
        ],
        default => [
            'border' => 'border-nacho-success/30',
            'bg' => 'bg-nacho-success/10',
            'title' => 'text-nacho-success',
            'titleKey' => 'components.results.accepted_title',
            'textKey' => 'components.results.accepted_text',
        ],
    };
@endphp

<div {{ $attributes->class(['rounded-xl border p-5', $config['border'], $config['bg']]) }}>
    <h3 class="{{ 'text-lg font-bold ' . $config['title'] }}">{{ __($config['titleKey']) }}</h3>
    <p class="mt-2 text-sm leading-relaxed text-nacho-dark/80">
        {{ $slot->isNotEmpty() ? $slot : __($config['textKey']) }}
    </p>
</div>
