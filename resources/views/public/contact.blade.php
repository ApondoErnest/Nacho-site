@extends('layouts.public')

@section('title', __('navigation.contact'))

@section('content')
    <div class="nacho-container nacho-section">
        <div class="grid gap-10 lg:grid-cols-2 lg:gap-12">
            <div class="space-y-8">
                <x-public.page-title :title="__('navigation.contact')" />

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-nacho-dark/10 sm:p-8">
                    <h2 class="text-lg font-semibold text-nacho-dark">
                        {{ app()->getLocale() === 'fr' ? config('centers.headquarters.label_fr') : config('centers.headquarters.label_en') }}
                    </h2>
                    <ul class="mt-4 space-y-3 text-sm text-nacho-dark/80">
                        @foreach (config('centers.headquarters.phones', []) as $phone)
                            <li>
                                <span class="font-semibold text-nacho-dark">{{ __('components.center.phone') }}:</span>
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="text-nacho-primary hover:text-nacho-primary-dark">{{ $phone }}</a>
                            </li>
                        @endforeach
                        <li>
                            <span class="font-semibold text-nacho-dark">Email:</span>
                            <a href="mailto:{{ config('centers.headquarters.email') }}" class="text-nacho-primary hover:text-nacho-primary-dark">{{ config('centers.headquarters.email') }}</a>
                        </li>
                        <li>
                            <span class="font-semibold text-nacho-dark">{{ app()->getLocale() === 'fr' ? 'Adresse' : 'Address' }}:</span>
                            {{ config('centers.headquarters.address') }}
                        </li>
                        <li>
                            <span class="font-semibold text-nacho-dark">{{ __('footer.postal_box') }}:</span>
                            {{ config('centers.headquarters.postal_box') }}
                        </li>
                    </ul>
                </div>

                <div>
                    <h2 class="mb-4 text-lg font-semibold text-nacho-dark">{{ __('navigation.centers') }}</h2>
                    <x-public.centers-grid />
                </div>
            </div>

            <div>
                <h2 class="mb-4 text-lg font-semibold text-nacho-dark">{{ app()->getLocale() === 'fr' ? 'Envoyer un message' : 'Send a message' }}</h2>
                <x-public.contact-form />
            </div>
        </div>
    </div>
@endsection
