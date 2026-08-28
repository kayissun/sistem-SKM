<section>
    <header class="flex items-start gap-3 mb-5">
        <div class="w-10 h-10 rounded-xl bg-[#EDE9FE] text-[#6D28D9] flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-user-pen"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-[#180733]">
                {{ __('Informasi Profil') }}
            </h2>
            <p class="mt-1 text-sm text-[#625B78]">
                {{ __("Perbarui nama dan alamat email akun kamu.") }}
            </p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama')" class="!text-[#180733] !font-semibold" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full focus:!border-[#7C3AED] focus:!ring-[#7C3AED]" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="!text-[#180733] !font-semibold" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full focus:!border-[#7C3AED] focus:!ring-[#7C3AED]" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-[#625B78]">
                        {{ __('Alamat email kamu belum diverifikasi.') }}

                        <button form="send-verification" class="underline text-sm font-semibold text-[#6D28D9] hover:text-[#2E1065] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#7C3AED]">
                            {{ __('Klik di sini untuk kirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Link verifikasi baru telah dikirim ke alamat email kamu.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="!bg-[#7C3AED] hover:!bg-[#5B21B6] focus:!ring-[#7C3AED]">{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
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