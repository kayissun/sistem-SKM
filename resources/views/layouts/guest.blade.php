<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:ital,wght@1,500;1,600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .kawung-bg {
                background-image: url("data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='72'%20height='72'%3E%3Cg%20fill='none'%20stroke='%23ffffff'%20stroke-opacity='0.14'%20stroke-width='1.1'%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(45%2036%2036)'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(90%2036%2036)'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(135%2036%2036)'/%3E%3Ccircle%20cx='36'%20cy='36'%20r='3.5'/%3E%3C/g%3E%3C/svg%3E");
                background-size: 72px 72px;
            }
        </style>
    </head>
    <body class="text-slate-900 antialiased">
        <div class="min-h-screen flex">

            <!-- Brand panel -->
            <div class="hidden lg:flex lg:w-[42%] relative flex-col justify-between overflow-hidden p-12 text-white"
                 style="background: linear-gradient(135deg,#7C3AED 0%,#5B21B6 55%,#2A0B5E 100%)">
                <div class="absolute inset-0 kawung-bg pointer-events-none"></div>

                <a href="/" class="relative z-10 flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-15">
                    <span class="flex flex-col leading-tight">
                        <span class="font-extrabold text-lg">SIPUAS</span>
                        <span class="text-xs text-white/60 font-semibold">Dinkesda Kab. Purworejo</span>
                    </span>
                </a>

                <div class="relative z-10">
                    <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-wide mb-5">
                        <i class="fa-solid fa-landmark text-amber-400"></i>
                       Sistem Informasi
                    </span>
                    <h1 class="text-3xl font-extrabold leading-tight mb-4">
                        Survei Kepuasan Masyarakat
                        <em class="not-italic font-semibold" style="font-family:'Fraunces',serif; font-style:italic;">Dinas Kesehatan Daerah</em>
                    </h1>
                    <p class="text-white/70 text-sm max-w-sm">
                        Pantau Indeks Kepuasan Masyarakat, kelola kuesioner tiap unit, dan lihat laporan real-time dalam satu sistem.
                    </p>
                </div>

                <p class="relative z-10 text-xs text-white/40">
                    &copy; {{ date('Y') }} SIPUAS &middot; Dinas Kesehatan Daerah Kabupaten Purworejo
                </p>
            </div>

            <!-- Form panel -->
            <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 bg-slate-50">
                <div class="w-full max-w-sm">
                    <div class="lg:hidden flex flex-col items-center mb-8">
                        <a href="/"><x-application-logo class="w-14 h-14 fill-current text-violet-700" /></a>
                        <span class="mt-3 font-extrabold text-slate-900">SIPUAS</span>
                        <span class="text-xs text-slate-500 font-semibold">Dinkesda Kab. Purworejo</span>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-100 p-8" style="box-shadow: 0 20px 40px -15px rgba(46,16,101,0.15)">
                        {{ $slot }}
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>