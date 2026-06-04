@extends('layouts.public')

@section('title', __($pageTitle))

@section('content')
    <div class="nacho-container nacho-section">
        <div class="mx-auto max-w-2xl rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-nacho-dark/10 sm:p-12">
            <h1 class="text-nacho-dark">{{ __($pageTitle) }}</h1>
            <p class="mt-4 text-nacho-dark/70">
                {{ app()->getLocale() === 'fr'
                    ? 'Contenu à venir — cette page sera construite dans une prochaine étape du plan de développement.'
                    : 'Content coming soon — this page will be built in an upcoming development step.' }}
            </p>
            <a href="{{ route('home') }}" class="btn-nacho-secondary mt-8 inline-flex">
                {{ __('navigation.home') }}
            </a>
        </div>
    </div>
@endsection
