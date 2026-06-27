@props([
    'position' => null,
])

<form {{ $attributes->merge(['action' => '#', 'method' => 'POST', 'class' => 'form-shell', 'enctype' => 'multipart/form-data']) }} novalidate>
    @csrf

    <x-public.form-field :label="__('components.forms.full_name')" name="full_name" required />
    <x-public.form-field :label="__('components.forms.email')" name="email" type="email" required />
    <x-public.form-field :label="__('components.forms.phone')" name="phone" type="tel" required />

    <x-public.form-field :label="__('components.forms.position')" name="position" :value="$position ?? ''" required />

    <x-public.form-field :label="__('components.forms.cv')" name="cv" type="file" required :hint="__('components.forms.pdf_only')" />
    <x-public.form-field :label="__('components.forms.cover_letter')" name="cover_letter" type="file" optional />

    <div class="pt-2">
        <button type="submit" class="btn-nacho-primary w-full sm:w-auto">
            {{ __('components.forms.submit_application') }}
        </button>
    </div>
</form>
