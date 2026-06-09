@extends('layouts.public')

@section('title', __('navigation.services'))

@section('content')
    <div class="nacho-container nacho-section space-y-10">
        <x-public.page-title
            :title="__('home.services.title')"
            :description="__('home.services.intro')"
        />

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach (config('home.services', []) as $service)
                <x-public.service-card
                    :title="__('home.services.' . $service['key'] . '.title')"
                    :description="__('home.services.' . $service['key'] . '.description')"
                    :href="route('services.index') . '#' . $service['slug']"
                    :icon="$service['icon']"
                    id="{{ $service['slug'] }}"
                />
            @endforeach
        </div>

        <x-public.cta-section
            variant="dark"
            :title="__('home.final_cta.title')"
            :text="__('home.final_cta.text')"
            :button-label="__('home.hero.cta_book')"
            :secondary-label="__('home.final_cta.secondary_tariffs')"
            :secondary-url="route('tariffs')"
            :tertiary-label="__('home.final_cta.secondary_contact')"
            :tertiary-url="route('contact')"
        />
    </div>
@endsection
