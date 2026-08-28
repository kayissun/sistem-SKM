<section>
    <header class="flex items-start gap-3 mb-5">
        <div class="w-10 h-10 rounded-xl bg-[#EDE9FE] text-[#6D28D9] flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-lock"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-[#180733]">
                {{ __('Ubah Kata Sandi') }}
            </h2>
            <p class="mt-1 text-sm text-[#625B78]">
                {{ __('Pastikan akun kamu memakai kata sandi yang panjang dan acak agar tetap aman.') }}
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Kata Sandi Saat Ini')" class="!text-[#180733] !font-semibold" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full focus:!border-[#7C3AED] focus:!ring-[#7C3AED]" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Kata Sandi Baru')" class="!text-[#180733] !font-semibold" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full focus:!border-[#7C3AED] focus:!ring-[#7C3AED]" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Kata Sandi')" class="!text-[#180733] !font-semibold" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full focus:!border-[#7C3AED] focus:!ring-[#7C3AED]" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="!bg-[#7C3AED] hover:!bg-[#5B21B6] focus:!ring-[#7C3AED]">{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-semibold text-[#15803D]"
                >{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>