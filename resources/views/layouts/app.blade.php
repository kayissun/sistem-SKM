<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
    </head>
    <body class="antialiased" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex" style="background:#F4F2FB">

            @include('layouts.navigation')

            <div class="flex-1 flex flex-col min-h-screen lg:ml-64">

                <!-- Top header -->
                <header class="sticky top-0 z-30 bg-white border-b border-violet-900/[.08] px-5 lg:px-8 py-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <button @click="sidebarOpen = true" class="lg:hidden shrink-0 w-9 h-9 rounded-lg bg-violet-50 text-violet-700 flex items-center justify-center">
                            <i class="fa-solid fa-bars"></i>
                        </button>

                        @isset($header)
                            <div class="min-w-0 truncate text-base lg:text-lg font-extrabold" style="color:#180733">
                                {{ $header }}
                            </div>
                        @endisset
                    </div>

                    <!-- Account dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2.5 border border-violet-900/[.12] bg-white rounded-full pl-1.5 pr-3 py-1.5 hover:bg-violet-50 transition">
                                <span class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background: linear-gradient(135deg,#7C3AED,#2A0B5E)">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                                <span class="hidden sm:block text-sm font-semibold text-slate-700">{{ Auth::user()->name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                <i class="fa-solid fa-user mr-2 text-violet-600"></i>{{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="fa-solid fa-right-from-bracket mr-2 text-red-500"></i>{{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </header>

                <!-- Page content -->
                <main class="flex-1 p-5 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>