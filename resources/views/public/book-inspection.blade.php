@extends('layouts.public')

@section('title', __('navigation.book'))

@section('content')
    <div class="nacho-container nacho-section">
        <div class="mx-auto max-w-2xl">
            <x-public.page-title
                :title="__('navigation.book')"
                :description="app()->getLocale() === 'fr'
                    ? 'Demande de réservation — 3 centres opérationnels uniquement.'
                    : 'Booking request — operational centers only.'"
            />
            <div class="mt-8">
                <x-public.booking-form />
            </div>
        </div>
    </div>
@endsection
