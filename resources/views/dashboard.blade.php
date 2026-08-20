<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-2xl border border-violet-900/[.08] p-8" style="box-shadow: 0 12px 28px -16px rgba(46,16,101,.18)">
            <div class="flex items-center gap-4">
                <span class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg shrink-0" style="background: linear-gradient(135deg,#7C3AED,#2A0B5E)">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
                <div>
                    <h3 class="font-extrabold text-lg" style="color:#180733">{{ __("You're logged in!") }}</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Selamat datang kembali, {{ Auth::user()->name }}.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>