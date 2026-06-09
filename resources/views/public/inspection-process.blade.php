@extends('layouts.public')

@section('title', __('navigation.inspection_process'))

@section('content')
    <div class="nacho-container nacho-section space-y-12">
        <x-public.page-title
            :title="__('navigation.inspection_process')"
            :description="app()->getLocale() === 'fr' ? 'Le déroulement de votre inspection, étape par étape.' : 'How your inspection works, step by step.'"
        />

        <x-public.process-steps />

        <section class="space-y-6">
            <h2 class="text-nacho-dark">{{ app()->getLocale() === 'fr' ? 'Comprendre votre résultat' : 'Understand your result' }}</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <x-public.inspection-result type="accepted" />
                <x-public.inspection-result type="suspended" />
                <x-public.inspection-result type="refused" />
            </div>
        </section>

        <x-public.cta-section />
    </div>
@endsection
