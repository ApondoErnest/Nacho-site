@extends('layouts.public')

@section('title', config('app.name'))

@section('content')
    <div class="nacho-container nacho-section space-y-12">
        <section class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-nacho-dark/10 sm:p-12">
            <p class="text-sm font-semibold uppercase tracking-wider text-nacho-primary">
                {{ app()->getLocale() === 'fr' ? 'Étape 4 — Mise en page publique' : 'Step 4 — Public layout' }}
            </p>
            <h1 class="mt-3 max-w-3xl text-nacho-dark">
                {{ app()->getLocale() === 'fr'
                    ? 'Inspection technique automobile professionnelle pour des routes plus sûres'
                    : 'Professional Vehicle Technical Inspection for Safer Roads' }}
            </h1>
            <p class="mt-4 max-w-2xl text-lg text-nacho-dark/80">
                {{ app()->getLocale() === 'fr'
                    ? 'Roulez en sécurité. Restez conforme. Faites confiance à NACHO.'
                    : 'Drive Safe. Stay Compliant. Trust NACHO.' }}
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                <a href="{{ route('book-inspection') }}" class="btn-nacho-primary">
                    {{ __('navigation.book') }}
                </a>
                <a href="{{ route('centers.index') }}" class="btn-nacho-secondary">
                    {{ __('navigation.centers') }}
                </a>
            </div>
        </section>

        <section class="grid gap-8 lg:grid-cols-2">
            <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-nacho-dark/10">
                <h2 class="mb-4 text-nacho-dark">{{ app()->getLocale() === 'fr' ? 'Palette de marque' : 'Brand palette' }}</h2>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach (['primary', 'primary-dark', 'dark', 'cream'] as $token)
                        <div class="overflow-hidden rounded-lg ring-1 ring-nacho-dark/10">
                            <div @class([
                                'h-12',
                                'bg-nacho-primary' => $token === 'primary',
                                'bg-nacho-primary-dark' => $token === 'primary-dark',
                                'bg-nacho-dark' => $token === 'dark',
                                'bg-nacho-cream' => $token === 'cream',
                            ])></div>
                            <p class="p-2 text-xs font-medium text-nacho-dark">nacho-{{ $token }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-nacho-dark/10">
                <h2 class="mb-4 text-nacho-dark">{{ app()->getLocale() === 'fr' ? 'Statuts d\'inspection' : 'Inspection statuses' }}</h2>
                <div class="space-y-3">
                    <p class="rounded-md bg-nacho-success/10 px-4 py-3 text-sm font-medium text-nacho-success">
                        {{ app()->getLocale() === 'fr' ? 'Accepté' : 'Accepted' }}
                    </p>
                    <p class="rounded-md bg-nacho-warning/10 px-4 py-3 text-sm font-medium text-nacho-warning">
                        {{ app()->getLocale() === 'fr' ? 'Suspendu' : 'Suspended' }}
                    </p>
                    <p class="rounded-md bg-nacho-danger/10 px-4 py-3 text-sm font-medium text-nacho-danger">
                        {{ app()->getLocale() === 'fr' ? 'Refusé' : 'Refused' }}
                    </p>
                </div>
            </div>
        </section>
    </div>
@endsection
