<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between flex-wrap gap-3 pb-4 border-b border-[#180733]/10">
            <div>
                <div class="flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-wider text-[#A66A0E] mb-1">
                    <span class="inline-block w-[18px] h-[3px] rounded-full bg-gradient-to-r from-[#7C3AED] to-[#2A0B5E]"></span>
                    Akun Saya
                </div>
                <h2 class="font-extrabold text-xl text-[#180733] leading-tight">
                    {{ __('Profil') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white border border-[#180733]/[0.06] shadow-sm sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.role-unit-info')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white border border-[#180733]/[0.06] shadow-sm sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white border border-[#180733]/[0.06] shadow-sm sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white border border-red-200 shadow-sm sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>