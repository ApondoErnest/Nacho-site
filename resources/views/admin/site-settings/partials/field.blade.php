@php
    $key = $definition['key'];
    $field = "settings.{$key}";
    $value = old($field, $setting->value);
    $type = $definition['type'];
    $isBoolean = $type === \App\Enums\SettingType::BOOLEAN;
    $isColor = $type === \App\Enums\SettingType::COLOR;
    $isImage = $type === \App\Enums\SettingType::IMAGE;
    $isMultiline = (bool) ($definition['multiline'] ?? false);
@endphp

<div @class([
    'rounded-lg border border-gray-200 bg-gray-50 p-4',
    'lg:col-span-2' => $isMultiline,
])>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <label for="setting-{{ $key }}" class="block text-sm font-bold text-gray-800">{{ $definition['label'] }}</label>
            <p class="mt-1 break-all font-mono text-xs text-gray-500">{{ $key }}</p>
        </div>
        <span class="inline-flex shrink-0 rounded-full bg-white px-2.5 py-1 text-xs font-bold text-gray-600 ring-1 ring-gray-200">
            {{ str($type->value)->title() }}
        </span>
    </div>

    <div class="mt-3">
        @if ($key === 'default_language')
            <select id="setting-{{ $key }}" name="settings[{{ $key }}]" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                <option value="fr" @selected($value === 'fr')>French</option>
                <option value="en" @selected($value === 'en')>English</option>
            </select>
        @elseif ($isBoolean)
            <input type="hidden" name="settings[{{ $key }}]" value="0">
            <label class="inline-flex min-h-11 items-center gap-3 rounded-md border border-gray-200 bg-white px-3 py-2 text-sm font-bold text-gray-700">
                <input
                    id="setting-{{ $key }}"
                    type="checkbox"
                    name="settings[{{ $key }}]"
                    value="1"
                    @checked((string) $value === '1' || $value === true)
                    class="rounded border-gray-300 text-nacho-primary shadow-sm focus:ring-nacho-primary"
                >
                <span>Enabled</span>
            </label>
        @elseif ($isColor)
            <div class="grid gap-3 sm:grid-cols-[3.5rem_minmax(0,1fr)]">
                <input
                    type="color"
                    value="{{ preg_match('/^#[0-9A-Fa-f]{6}$/', (string) $value) ? $value : '#b45309' }}"
                    class="h-10 w-14 rounded-md border border-gray-300 bg-white p-1"
                    aria-label="{{ $definition['label'] }} swatch"
                    onchange="this.nextElementSibling.value = this.value"
                >
                <input
                    id="setting-{{ $key }}"
                    name="settings[{{ $key }}]"
                    value="{{ $value }}"
                    placeholder="#b45309"
                    class="block w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary"
                >
            </div>
        @elseif ($isMultiline)
            <textarea id="setting-{{ $key }}" name="settings[{{ $key }}]" rows="4" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ $value }}</textarea>
        @else
            <input
                id="setting-{{ $key }}"
                name="settings[{{ $key }}]"
                value="{{ $value }}"
                @if ($key === 'contact_email' || $key === 'careers_general_application_email') type="email" @else type="text" @endif
                @class([
                    'block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary',
                    'font-mono' => $isImage,
                ])
            >
        @endif

        <x-input-error :messages="$errors->get($field)" class="mt-2" />
    </div>

    <p class="mt-2 text-xs font-semibold text-gray-500">{{ $definition['help'] }}</p>
</div>
