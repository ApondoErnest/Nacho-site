<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <link rel="icon" href="{{ asset(config('branding.favicon')) }}" type="image/png">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body class="flex min-h-screen flex-col bg-nacho-cream font-sans text-nacho-dark antialiased">
        <x-public.header />

        <main class="flex-1">
            @yield('content')
        </main>

        <x-public.footer />
        <x-public.cookie-banner />
    </body>
</html>
