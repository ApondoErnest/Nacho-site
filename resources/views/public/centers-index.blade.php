@extends('layouts.public')

@section('title', __('navigation.centers'))

@section('content')
    <div class="nacho-container nacho-section space-y-10">
        <x-public.page-title
            :title="__('navigation.centers')"
            :description="app()->getLocale() === 'fr'
                ? '3 centres opérationnels et 2 centres en construction — ouverture avant novembre 2026.'
                : '3 operational centers and 2 centers under construction — opening before November 2026.'"
        />

        <x-public.centers-grid />
    </div>
@endsection
