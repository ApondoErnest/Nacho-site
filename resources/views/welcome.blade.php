@extends('layouts.public')

@section('title', config('app.name'))

@section('content')
    <x-public.hero-split>
        <x-slot:actions>
            <a href="{{ route('book-inspection') }}" class="hero-action-primary">
                <x-lucide-calendar-check class="h-5 w-5" aria-hidden="true" />
                {{ __('home.hero.cta_book') }}
            </a>
            <a href="{{ route('inspection-process') }}" class="hero-action-secondary">
                <x-lucide-map-pin class="h-5 w-5" aria-hidden="true" />
                {{ __('home.hero.cta_track_status') }}
            </a>
        </x-slot:actions>
    </x-public.hero-split>

    <x-public.technical-checks-grid />

    <div class="nacho-container space-y-14 py-10 sm:space-y-16 sm:py-12 lg:space-y-20">
        <x-public.about-preview-cards />

        <x-public.process-showcase />

        <x-public.inspection-centers-showcase />

        <x-public.why-choose-showcase />

        <x-public.inspection-fees-table />

        <x-public.latest-articles-showcase />

        <x-public.client-testimonials-showcase />
    </div>
@endsection
