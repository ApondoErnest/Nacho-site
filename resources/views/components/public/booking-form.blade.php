<form {{ $attributes->merge(['action' => '#', 'method' => 'POST', 'class' => 'form-shell']) }} novalidate>
    @csrf

    <x-public.form-field :label="__('components.forms.full_name')" name="full_name" required />
    <x-public.form-field :label="__('components.forms.phone')" name="phone" type="tel" required />
    <x-public.form-field :label="__('components.forms.email')" name="email" type="email" optional />

    <x-public.form-field :label="__('components.forms.center')" name="center_id" type="select" required>
        <option value="">{{ __('components.forms.select_placeholder') }}</option>
        @foreach (app(\App\Support\PublicSiteData::class)->centers()->where('status', 'operational') as $center)
            <option value="{{ $center['slug'] }}">{{ $center['name'] }} — {{ $center['city'] }}</option>
        @endforeach
    </x-public.form-field>

    <x-public.form-field :label="__('components.forms.registration')" name="vehicle_registration" required />
    <x-public.form-field :label="__('components.forms.vehicle_category')" name="vehicle_category" type="select" required>
        <option value="">{{ __('components.forms.select_placeholder') }}</option>
        <option value="light">{{ app()->getLocale() === 'fr' ? 'Véhicule léger' : 'Light vehicle' }}</option>
        <option value="taxi">{{ app()->getLocale() === 'fr' ? 'Taxi' : 'Taxi' }}</option>
        <option value="heavy">{{ app()->getLocale() === 'fr' ? 'Poids lourd' : 'Heavy vehicle' }}</option>
    </x-public.form-field>

    <x-public.form-field :label="__('components.forms.service_type')" name="service_type" type="select" required>
        <option value="">{{ __('components.forms.select_placeholder') }}</option>
        <option value="periodic">{{ app()->getLocale() === 'fr' ? 'Visite périodique' : 'Periodic inspection' }}</option>
        <option value="counter">{{ app()->getLocale() === 'fr' ? 'Contre-visite' : 'Counter-visit' }}</option>
    </x-public.form-field>

    <div class="grid gap-5 sm:grid-cols-2">
        <x-public.form-field :label="__('components.forms.preferred_date')" name="preferred_date" type="date" required />
        <x-public.form-field :label="__('components.forms.preferred_time')" name="preferred_time" type="time" required />
    </div>

    <x-public.form-field :label="__('components.forms.documents')" name="documents" type="file" optional />
    <x-public.form-field :label="__('components.forms.comment')" name="comment" type="textarea" optional />

    <div class="flex items-start gap-3">
        <input
            type="checkbox"
            id="booking_consent"
            name="consent"
            value="1"
            required
            class="mt-1 rounded border-gray-300 text-nacho-primary focus:ring-nacho-primary"
        />
        <label for="booking_consent" class="text-sm text-nacho-dark/80">
            {{ __('components.forms.consent_booking') }}
        </label>
    </div>

    <div class="pt-2">
        <button type="submit" class="btn-nacho-primary w-full sm:w-auto">
            {{ __('components.forms.submit_booking') }}
        </button>
    </div>
</form>
