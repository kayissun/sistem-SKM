<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">
</head>

<style>
        /* loading bar */
    #nprogress .bar {
      background: linear-gradient(90deg, #7C3AED, #C88719) !important;
      height: 3px !important;
    }
    #nprogress .peg {
      box-shadow: 0 0 10px #7C3AED, 0 0 5px #C88719 !important;
    }
    /* sembunyikan spinner bawaan pojok kanan atas, cukup bar saja */
    #nprogress .spinner { display: none; }
</style>

<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-slate-900">Masuk ke Sistem</h2>
        <p class="text-sm text-slate-500 mt-1">Silakan masuk menggunakan akun yang terdaftar.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative mt-1.5">
                <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <x-text-input id="email" class="block w-full pl-10" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1.5">
                <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <x-text-input id="password" class="block w-full pl-10" type="password" name="password" required autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-violet-600 shadow-sm focus:ring-violet-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-violet-600 hover:text-violet-800 font-semibold" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full">
            {{ __('Log in') }}
            <i class="fa-solid fa-arrow-right"></i>
        </x-primary-button>
    </form>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    <script>
        NProgress.configure({ showSpinner: false, trickleSpeed: 120, minimum: 0.15 });

        // Mulai bar sesegera mungkin saat halaman ini pertama kali dimuat,
        // lalu selesaikan begitu semua resource siap.
        NProgress.start();
        window.addEventListener('load', () => NProgress.done());

        // Jalankan bar setiap kali user KLIK link yang akan berpindah halaman
        // (link internal, bukan anchor #, bukan target="_blank", bukan mailto/tel).
        document.addEventListener('click', function (e) {
        const link = e.target.closest('a');
        if (!link) return;

        const url = link.getAttribute('href') || '';
        const isSameOrigin = link.hostname === window.location.hostname;
        const isHash = url.startsWith('#');
        const isNewTab = link.target === '_blank';
        const isSpecial = url.startsWith('mailto:') || url.startsWith('tel:') || url.startsWith('javascript:');

        if (isSameOrigin && !isHash && !isNewTab && !isSpecial) {
            NProgress.start();
        }
        });

        // Jalankan bar juga saat user SUBMIT form (mis. form login)
        document.addEventListener('submit', function () {
        NProgress.start();
        });
    </script>
</x-guest-layout>