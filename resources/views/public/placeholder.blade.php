@extends('layouts.public')

@section('title', isset($page) && $page ? $page->localized('title') : __($pageTitle))

@section('content')
    <div class="nacho-container nacho-section">
        <div class="mx-auto max-w-2xl rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-nacho-dark/10 sm:p-12">
            @if (isset($page) && $page)
                <h1 class="text-nacho-dark">{{ $page->localized('title') }}</h1>
                <div class="mt-4 text-nacho-dark/70">
                    {!! nl2br(e($page->localized('content'))) !!}
                </div>
            @else
                <h1 class="text-nacho-dark">{{ __($pageTitle) }}</h1>
                <p class="mt-4 text-nacho-dark/70">
                    {{ __('components.forms.placeholder_content') }}
                </p>
            @endif
            <a href="{{ route('home') }}" class="btn-nacho-secondary mt-8 inline-flex">
                {{ __('navigation.home') }}
            </a>
        </div>
    </div>
@endsection
