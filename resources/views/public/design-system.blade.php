@extends('layouts.public')

@section('title', __('components.design_system'))

@section('content')
    <div class="nacho-container nacho-section space-y-16">
        <x-public.page-title :title="__('components.design_system')">
            <x-slot:breadcrumb>
                <a href="{{ route('home') }}" class="text-nacho-primary hover:text-nacho-primary-dark">{{ __('navigation.home') }}</a>
            </x-slot:breadcrumb>
        </x-public.page-title>

        <section class="space-y-6">
            <h2 class="text-nacho-dark">Hero</h2>
            <x-public.hero>
                <x-slot:actions>
                    <a href="{{ route('book-inspection') }}" class="btn-nacho-primary">{{ __('navigation.book') }}</a>
                    <a href="{{ route('centers.index') }}" class="btn-nacho-secondary !border-white/30 !bg-white/10 !text-white hover:!bg-white/20">{{ __('navigation.centers') }}</a>
                </x-slot:actions>
            </x-public.hero>
        </section>

        <section class="space-y-4">
            <h2 class="text-nacho-dark">Trust strip</h2>
            <x-public.trust-strip />
        </section>

        <section class="space-y-4">
            <h2 class="text-nacho-dark">Alerts</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <x-public.alert type="success">{{ app()->getLocale() === 'fr' ? 'Exemple de message de succès.' : 'Sample success message.' }}</x-public.alert>
                <x-public.alert type="error">{{ app()->getLocale() === 'fr' ? 'Exemple de message d\'erreur.' : 'Sample error message.' }}</x-public.alert>
                <x-public.alert type="warning">{{ app()->getLocale() === 'fr' ? 'Exemple d\'avertissement.' : 'Sample warning message.' }}</x-public.alert>
                <x-public.alert type="info">{{ app()->getLocale() === 'fr' ? 'Exemple d\'information.' : 'Sample info message.' }}</x-public.alert>
            </div>
        </section>

        <section class="space-y-6">
            <h2 class="text-nacho-dark">{{ __('navigation.centers') }}</h2>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <x-public.center-card
                    name="NACHO Yaoundé"
                    city="Yaoundé"
                    status="operational"
                    :landmark="app()->getLocale() === 'fr' ? 'Près du rond-point…' : 'Near main roundabout…'"
                    :address="app()->getLocale() === 'fr' ? 'Adresse exemple' : 'Sample address'"
                    :vehicle-categories="app()->getLocale() === 'fr' ? 'Légers, taxis' : 'Light vehicles, taxis'"
                    href="#"
                />
                <x-public.center-card
                    name="NACHO Garoua"
                    city="Garoua"
                    status="under_construction"
                    :landmark="app()->getLocale() === 'fr' ? 'Zone industrielle' : 'Industrial zone'"
                    href="#"
                />
            </div>
        </section>

        <section class="space-y-6">
            <h2 class="text-nacho-dark">{{ __('navigation.services') }}</h2>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <x-public.service-card
                    :title="app()->getLocale() === 'fr' ? 'Visite périodique' : 'Periodic Technical Inspection'"
                    :description="app()->getLocale() === 'fr' ? 'Description courte du service.' : 'Short service description.'"
                    icon="🔧"
                />
            </div>
        </section>

        <section class="space-y-6">
            <h2 class="text-nacho-dark">{{ __('navigation.blog') }}</h2>
            <div class="grid gap-6 md:grid-cols-2">
                <x-public.blog-card
                    :title="app()->getLocale() === 'fr' ? 'Préparer son véhicule avant l\'inspection' : 'How to prepare your vehicle before inspection'"
                    :excerpt="app()->getLocale() === 'fr' ? 'Extrait d\'article sur la sécurité routière.' : 'Road safety article excerpt.'"
                    :category="app()->getLocale() === 'fr' ? 'Sécurité routière' : 'Road safety'"
                    date="2026-01-15"
                />
            </div>
        </section>

        <section class="space-y-6">
            <h2 class="text-nacho-dark">{{ __('navigation.careers') }}</h2>
            <div class="grid gap-6 md:grid-cols-2">
                <x-public.career-card
                    :title="app()->getLocale() === 'fr' ? 'Inspecteur technique' : 'Technical Inspector'"
                    location="Douala"
                    :department="app()->getLocale() === 'fr' ? 'Exploitation' : 'Operations'"
                    :employment-type="__('components.career.full_time')"
                    deadline="2026-07-01"
                />
            </div>
        </section>

        <section class="space-y-6">
            <h2 class="text-nacho-dark">{{ __('navigation.tariffs') }}</h2>
            @php
                $tariffRows = [
                    [
                        'category' => app()->getLocale() === 'fr' ? 'Catégorie 1' : 'Category 1',
                        'vehicle_type' => app()->getLocale() === 'fr' ? 'Véhicule léger' : 'Light vehicle',
                        'price' => '15 000 FCFA',
                        'validity' => app()->getLocale() === 'fr' ? '1 an' : '1 year',
                        'documents' => app()->getLocale() === 'fr' ? 'Carte grise, assurance' : 'Registration, insurance',
                    ],
                ];
            @endphp
            <x-public.tariff-table :rows="$tariffRows" />
            <x-public.tariff-card
                :category="$tariffRows[0]['category']"
                :vehicle-type="$tariffRows[0]['vehicle_type']"
                :price="$tariffRows[0]['price']"
                :validity="$tariffRows[0]['validity']"
                :documents="$tariffRows[0]['documents']"
            />
        </section>

        <section class="space-y-6">
            <h2 class="text-nacho-dark">{{ __('navigation.inspection_process') }}</h2>
            <x-public.process-steps />
            <div class="grid gap-4 md:grid-cols-3">
                <x-public.inspection-result type="accepted" />
                <x-public.inspection-result type="suspended" />
                <x-public.inspection-result type="refused" />
            </div>
        </section>

        <section class="space-y-6">
            <h2 class="text-nacho-dark">CTA</h2>
            <x-public.cta-section />
        </section>

        <section class="space-y-4">
            <h2 class="text-nacho-dark">Pagination</h2>
            <x-public.pagination />
        </section>

        <section class="space-y-8">
            <h2 class="text-nacho-dark">Forms (UI only)</h2>
            <div class="grid gap-10 lg:grid-cols-2">
                <div>
                    <h3 class="mb-4 text-lg font-semibold">{{ __('navigation.book') }}</h3>
                    <x-public.booking-form />
                </div>
                <div>
                    <h3 class="mb-4 text-lg font-semibold">{{ __('navigation.contact') }}</h3>
                    <x-public.contact-form />
                </div>
            </div>
            <div class="max-w-xl">
                <h3 class="mb-4 text-lg font-semibold">{{ __('navigation.careers') }} — apply</h3>
                <x-public.career-application-form :position="app()->getLocale() === 'fr' ? 'Inspecteur technique' : 'Technical Inspector'" />
            </div>
        </section>
    </div>
@endsection
