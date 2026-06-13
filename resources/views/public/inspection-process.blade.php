@extends('layouts.public')

@section('title', __('inspection_process.meta_title'))

@section('content')
    @php
        $heroProofItems = __('inspection_process.hero.proof');
    @endphp

    <section class="inspection-process-hero" aria-labelledby="inspection-process-hero-title">
        <img
            src="{{ asset('images/hero-inspection-process.png') }}"
            alt=""
            class="inspection-process-hero-image"
            loading="eager"
            fetchpriority="high"
        />
        <div class="inspection-process-hero-overlay" aria-hidden="true"></div>

        <div class="inspection-process-hero-inner">
            <div class="inspection-process-hero-copy">
                <p class="inspection-process-hero-kicker">
                    {{ __('inspection_process.hero.eyebrow') }}
                </p>

                <h1 id="inspection-process-hero-title" class="inspection-process-hero-title">
                    <span>{{ __('inspection_process.hero.title_line_1') }}</span>
                    <span>{{ __('inspection_process.hero.title_line_2') }}</span>
                </h1>

                <p class="inspection-process-hero-text">
                    {{ __('inspection_process.hero.subtitle') }}
                </p>

                <div class="inspection-process-proof-grid" aria-label="{{ __('inspection_process.hero.proof_label') }}">
                    @foreach ($heroProofItems as $item)
                        <div class="inspection-process-proof-item">
                            <span class="inspection-process-proof-icon" aria-hidden="true">
                                <x-dynamic-component :component="'lucide-' . $item['icon']" />
                            </span>
                            <span>{{ $item['title'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="nacho-container nacho-section space-y-12">
        <x-public.inspection-journey />
        <x-public.inspection-preparation />
    </div>
@endsection
