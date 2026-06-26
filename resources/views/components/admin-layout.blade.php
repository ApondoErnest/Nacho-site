@props([
    'title' => 'Admin',
    'eyebrow' => 'NACHO Admin',
    'pendingBookings' => 0,
    'unreadMessages' => 0,
])

@php
    $navigation = [
        ['label' => 'Dashboard', 'route' => 'admin.home', 'ability' => 'dashboard.view', 'icon' => 'layout-dashboard'],
        ['label' => 'Centers', 'route' => 'admin.centers.index', 'ability' => 'centers.view', 'icon' => 'building-2'],
        ['label' => 'Services', 'route' => 'admin.services.index', 'ability' => 'services.view', 'icon' => 'clipboard-check'],
        ['label' => 'Tariffs', 'route' => 'admin.tariffs.index', 'ability' => 'tariffs.view', 'icon' => 'banknote'],
        ['label' => 'Bookings', 'route' => 'admin.bookings.index', 'ability' => 'bookings.view', 'icon' => 'calendar-days'],
        ['label' => 'Messages', 'route' => 'admin.contact-messages.index', 'ability' => 'contact-messages.view', 'icon' => 'inbox'],
        ['label' => 'Blog', 'route' => 'admin.blog-posts.index', 'ability' => 'blog.view', 'icon' => 'newspaper'],
        ['label' => 'Careers', 'route' => 'admin.career-posts.index', 'ability' => 'careers.view', 'icon' => 'briefcase-business'],
        ['label' => 'Pages', 'route' => 'admin.pages.index', 'ability' => 'pages.view', 'icon' => 'file-text'],
        ['label' => 'Media', 'route' => 'admin.media.index', 'ability' => 'media.view', 'icon' => 'image'],
        ['label' => 'Users', 'route' => 'admin.users.index', 'ability' => 'users.view', 'icon' => 'users'],
        ['label' => 'Roles', 'route' => 'admin.roles.index', 'ability' => 'roles.view', 'icon' => 'shield-check'],
        ['label' => 'Settings', 'route' => 'admin.site-settings.index', 'ability' => 'site-settings.view', 'icon' => 'settings'],
    ];

    $visibleNavigation = collect($navigation)
        ->filter(fn (array $item) => \App\Support\AdminAccess::can(auth()->user(), $item['ability']))
        ->values();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }} | {{ config('app.name', 'NACHO Vehicle Inspection') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div
            x-data="{ sidebarOpen: false }"
            class="min-h-screen bg-[#f5f7fa] text-gray-900"
        >
            <aside
                x-cloak
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-gray-950/45 lg:hidden"
                @click="sidebarOpen = false"
                aria-hidden="true"
            ></aside>

            <aside
                class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-gray-200 bg-white transition-transform duration-200 lg:translate-x-0"
                :class="{ 'translate-x-0': sidebarOpen }"
                aria-label="Admin navigation"
            >
                <div class="flex h-20 items-center justify-between border-b border-gray-200 px-5">
                    <a href="{{ route('admin.home') }}" class="inline-flex flex-col rounded-md">
                        <span class="text-xl font-extrabold leading-none text-nacho-primary">NACHO</span>
                        <span class="mt-1 text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Admin</span>
                    </a>
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-900 lg:hidden"
                        @click="sidebarOpen = false"
                    >
                        <span class="sr-only">Close navigation</span>
                        <x-lucide-x class="h-5 w-5" aria-hidden="true" />
                    </button>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
                    @foreach ($visibleNavigation as $item)
                        @php
                            $routeExists = Route::has($item['route']);
                            $isActive = $routeExists && request()->routeIs($item['route']);
                            $itemClasses = $isActive
                                ? 'bg-nacho-primary text-white shadow-sm shadow-nacho-primary/20'
                                : 'text-gray-700 hover:bg-gray-100 hover:text-gray-950';
                        @endphp

                        @if ($routeExists)
                            <a
                                href="{{ route($item['route']) }}"
                                @class([
                                    'group flex min-h-11 items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold',
                                    $itemClasses,
                                ])
                            >
                                <x-dynamic-component :component="'lucide-' . $item['icon']" class="h-5 w-5 shrink-0" aria-hidden="true" />
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @else
                            <span class="group flex min-h-11 items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold text-gray-400">
                                <x-dynamic-component :component="'lucide-' . $item['icon']" class="h-5 w-5 shrink-0" aria-hidden="true" />
                                <span>{{ $item['label'] }}</span>
                            </span>
                        @endif
                    @endforeach
                </nav>

                <div class="border-t border-gray-200 p-4">
                    <a href="{{ route('home') }}" class="flex min-h-11 items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 hover:text-gray-950">
                        <x-lucide-external-link class="h-5 w-5" aria-hidden="true" />
                        <span>View public site</span>
                    </a>
                </div>
            </aside>

            <div class="lg:pl-72">
                <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/95 backdrop-blur">
                    <div class="flex min-h-20 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                        <div class="flex min-w-0 items-center gap-3">
                            <button
                                type="button"
                                class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-100 hover:text-gray-950 lg:hidden"
                                @click="sidebarOpen = true"
                            >
                                <span class="sr-only">Open navigation</span>
                                <x-lucide-menu class="h-5 w-5" aria-hidden="true" />
                            </button>
                            <div class="min-w-0">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-nacho-primary">{{ $eyebrow }}</p>
                                <h1 class="mt-1 truncate text-xl font-bold tracking-normal text-gray-950 sm:text-2xl">{{ $title }}</h1>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                            <div class="hidden items-center gap-2 rounded-md border border-orange-200 bg-orange-50 px-3 py-2 text-xs font-bold text-nacho-primary sm:flex">
                                <x-lucide-calendar-clock class="h-4 w-4" aria-hidden="true" />
                                <span>{{ number_format((int) $pendingBookings) }} pending</span>
                            </div>
                            <div class="hidden items-center gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-bold text-gray-700 md:flex">
                                <x-lucide-mail-warning class="h-4 w-4 text-nacho-primary" aria-hidden="true" />
                                <span>{{ number_format((int) $unreadMessages) }} unread</span>
                            </div>

                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex min-h-11 items-center gap-3 rounded-md border border-gray-200 bg-white px-2.5 py-2 text-left text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-gray-900 text-xs font-bold uppercase text-white">
                                            {{ str(auth()->user()->name)->substr(0, 1) }}
                                        </span>
                                        <span class="hidden min-w-0 sm:block">
                                            <span class="block max-w-36 truncate text-gray-950">{{ auth()->user()->name }}</span>
                                            <span class="block max-w-36 truncate text-xs font-medium text-gray-500">{{ auth()->user()->role?->name }}</span>
                                        </span>
                                        <x-lucide-chevron-down class="h-4 w-4 text-gray-400" aria-hidden="true" />
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        Profile
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link
                                            :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                        >
                                            Log out
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
