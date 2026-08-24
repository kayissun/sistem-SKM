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
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
            
        .kawung-bg {
            background-image: url("data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='72'%20height='72'%3E%3Cg%20fill='none'%20stroke='%23E4A63B'%20stroke-opacity='0.25'%20stroke-width='1.1'%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(45%2036%2036)'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(90%2036%2036)'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(135%2036%2036)'/%3E%3Ccircle%20cx='36'%20cy='36'%20r='3.5'/%3E%3C/g%3E%3C/svg%3E");
            background-size: 72px 72px;
}
    </style>
    </head>
    <body class="text-slate-900 antialiased bg-slate-50">
        
        <div class="min-h-screen flex">                
            
            <!-- Brand / info panel (Kiri - Ungu Gradient + Kawung BG) -->
            <div class="hidden lg:flex lg:w-[42%] relative flex-col justify-between overflow-hidden p-12 text-white"
                 style="background: linear-gradient(135deg, #7C3AED 0%, #5B21B6 55%, #2A0B5E 100%)">
                
                <!-- Pattern Kawung Overlay -->
                <div class="absolute inset-0 kawung-bg pointer-events-none"></div>

                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div>
                        {{-- Lockup logo + nama sistem --}}
                        <a href="/" class="flex items-center gap-3 mb-10">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo SKM" class="w-16 h-16 rounded-lg object-contain bg-white/10 p-1 backdrop-blur-sm">
                            <span class="flex flex-col leading-tight">
                                <span class="font-extrabold text-lg text-white">Survei Kepuasan Masyarakat</span>
                                <span class="text-xs text-purple-200 font-semibold">Dinkesda Kab. Purworejo</span>
                            </span>
                        </a>

                        {{-- Baris logo instansi/mitra (dengan background netral tipis agar logo terlihat jelas) --}}
                        <div class="flex items-center gap-4 flex-wrap mb-10 p-3 rounded-xl bg-white/10 backdrop-blur-md">
                            <img src="{{ asset('images/logo-dinkes.png') }}" alt="Logo Dinas Kesehatan" class="h-9 w-auto object-contain">
                            <img src="{{ asset('images/logo-kemenkes.png') }}" alt="Logo Kementerian Kesehatan" class="h-9 w-auto object-contain">
                            <img src="{{ asset('images/logo-puskesmas.png') }}" alt="Logo Puskesmas" class="h-9 w-auto object-contain">
                            <img src="{{ asset('images/logo-pemkab.png') }}" alt="Logo Pemerintah Kabupaten Purworejo" class="h-9 w-auto object-contain">
                        </div>

                        {{-- Judul + penjelasan --}}
                        <h3 class="font-extrabold text-xl mb-3 text-amber-300">Apa itu SKM?</h3>
                        <p class="text-sm text-purple-100 leading-relaxed text-justify mb-4">
                            SKM (Sistem Informasi Survei Kepuasan Masyarakat) adalah sistem digital resmi
                            milik Dinas Kesehatan Daerah Kabupaten Purworejo yang digunakan seluruh Puskesmas
                            dan RSU untuk mengumpulkan penilaian warga terhadap layanan kesehatan yang diterima,
                            mengacu pada 9 unsur pelayanan sesuai Permenpan RB No. 14 Tahun 2017.
                        </p>
                        <p class="text-sm text-purple-100 leading-relaxed text-justify">
                            Dengan data yang terkumpul secara real-time dari tiap unit layanan, Dinas Kesehatan
                            dapat memantau mutu pelayanan, menindaklanjuti keluhan lebih cepat, dan menyusun
                            kebijakan peningkatan layanan berbasis bukti.
                        </p>
                    </div>

                    <p class="pt-10 text-xs text-purple-300">
                        &copy; {{ date('Y') }} SKM &middot; Dinas Kesehatan Daerah Kabupaten Purworejo
                    </p>
                </div>
            </div>

            <!-- Form panel (Kanan) -->
            <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 bg-slate-50">
                <div class="w-full max-w-sm">
                    
                    {{-- Tampilan Mobile --}}
                <div class="lg:hidden flex flex-col items-center mb-8">
                    <a href="/">
                        <!-- Pakai w-12 h-12 (48px) atau w-16 h-16 (64px) agar pas di mobile -->
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SKM" class="w-16 h-16 rounded-lg object-contain bg-slate-100 p-2 border border-slate-200">
                    </a>
                    <span class="mt-3 font-extrabold text-slate-900">Survei Kepuasan Masyarakat</span>
                    <span class="text-xs text-slate-500 font-semibold">Dinkesda Kab. Purworejo</span>
                </div>

                    {{-- Box Form Login/Register --}}
                    <div class="bg-white rounded-2xl border border-slate-100 p-8" style="box-shadow: 0 20px 40px -15px rgba(46,16,101,0.15)">
                        {{ $slot }}
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>