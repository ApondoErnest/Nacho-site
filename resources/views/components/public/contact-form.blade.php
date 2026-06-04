<form {{ $attributes->merge(['action' => '#', 'method' => 'POST', 'class' => 'form-shell']) }} novalidate>
    @csrf

    <x-public.form-field :label="__('components.forms.full_name')" name="full_name" required />
    <x-public.form-field :label="__('components.forms.email')" name="email" type="email" required />
    <x-public.form-field :label="__('components.forms.phone')" name="phone" type="tel" required />
    <x-public.form-field :label="__('components.forms.subject')" name="subject" required />
    <x-public.form-field :label="__('components.forms.message')" name="message" type="textarea" required />

    <div class="pt-2">
        <button type="submit" class="btn-nacho-primary w-full sm:w-auto">
            {{ __('components.forms.submit_contact') }}
        </button>
    </div>
</form>
