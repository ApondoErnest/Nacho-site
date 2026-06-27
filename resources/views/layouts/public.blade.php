<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php
            $seo = $seo ?? app(\App\Support\SeoMeta::class)->forCurrentRequest();
            $seoTitle = $seo['title'] ?? trim($__env->yieldContent('title', config('app.name')));
            $seoDescription = $seo['description'] ?? null;
            $seoCanonical = $seo['canonical'] ?? url()->current();
            $seoImage = $seo['image'] ?? asset(__('seo.default_image'));
            $seoType = $seo['type'] ?? 'website';
            $seoSiteName = $seo['siteName'] ?? __('seo.site_name');
            $seoLocale = $seo['locale'] ?? (app()->getLocale() === 'fr' ? 'fr_CM' : 'en_CM');
            $seoRobots = $seo['robots'] ?? 'index,follow';
            $seoJsonLd = $seo['jsonLd'] ?? [];
        @endphp
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="{{ $seoRobots }}">
        @if ($seoDescription)
            <meta name="description" content="{{ $seoDescription }}">
        @endif

        <title>{{ $seoTitle }}</title>
        <link rel="canonical" href="{{ $seoCanonical }}">

        <meta property="og:site_name" content="{{ $seoSiteName }}">
        <meta property="og:locale" content="{{ $seoLocale }}">
        <meta property="og:type" content="{{ $seoType }}">
        <meta property="og:title" content="{{ $seoTitle }}">
        @if ($seoDescription)
            <meta property="og:description" content="{{ $seoDescription }}">
        @endif
        <meta property="og:url" content="{{ $seoCanonical }}">
        <meta property="og:image" content="{{ $seoImage }}">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seoTitle }}">
        @if ($seoDescription)
            <meta name="twitter:description" content="{{ $seoDescription }}">
        @endif
        <meta name="twitter:image" content="{{ $seoImage }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @foreach ($seoJsonLd as $schema)
            <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
        @endforeach

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body class="flex min-h-screen flex-col bg-nacho-cream font-sans text-nacho-dark antialiased">
        <x-public.header />

        <main class="flex-1">
            @yield('content')
        </main>

        <x-public.footer />
        <x-public.floating-booking-button />
        <x-public.cookie-banner />
    </body>
</html>
