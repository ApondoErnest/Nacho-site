@props([
    'label',
    'name',
    'type' => 'text',
    'required' => false,
    'optional' => false,
    'hint' => null,
    'id' => null,
    'value' => null,
])

@php
    $id = $id ?? $name;
@endphp

<div {{ $attributes->class(['']) }}>
    <label for="{{ $id }}" class="form-label">
        {{ $label }}
        @if ($required)
            <span class="text-nacho-danger" aria-hidden="true">*</span>
            <span class="sr-only">({{ __('components.forms.required') }})</span>
        @elseif ($optional)
            <span class="ml-1 text-xs font-normal text-nacho-dark/50">({{ __('components.forms.optional') }})</span>
        @endif
    </label>

    @if ($type === 'textarea')
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            rows="{{ $attributes->get('rows', 4) }}"
            @class(['form-input', $attributes->get('class')])
            @if ($required) required @endif
        >{{ $slot }}</textarea>
    @elseif ($type === 'select')
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            @class(['form-input', $attributes->get('class')])
            @if ($required) required @endif
        >
            {{ $slot }}
        </select>
    @elseif ($type === 'file')
        <input
            type="file"
            id="{{ $id }}"
            name="{{ $name }}"
            @class(['form-input file:mr-4 file:rounded-md file:border-0 file:bg-nacho-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-nacho-primary-dark', $attributes->get('class')])
            @if ($required) required @endif
        />
    @else
        <input
            type="{{ $type }}"
            id="{{ $id }}"
            name="{{ $name }}"
            @if ($value !== null) value="{{ $value }}" @endif
            @class(['form-input', $attributes->get('class')])
            @if ($required) required @endif
        />
    @endif

    @if ($hint)
        <p class="form-hint">{{ $hint }}</p>
    @endif
</div>
