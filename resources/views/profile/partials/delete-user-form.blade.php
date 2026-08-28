<section class="space-y-6">
    <header class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-red-700">
                {{ __('Hapus Akun') }}
            </h2>

            <p class="mt-1 text-sm text-[#625B78]">
                {{ __('Setelah akun kamu dihapus, seluruh data terkait akan dihapus secara permanen. Unduh data yang ingin kamu simpan sebelum melanjutkan.') }}
            </p>
        </div>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Hapus Akun') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-[#180733]">
                {{ __('Yakin ingin menghapus akun ini?') }}
            </h2>

            <p class="mt-1 text-sm text-[#625B78]">
                {{ __('Setelah akun dihapus, seluruh data terkait akan dihapus secara permanen. Masukkan kata sandi untuk mengonfirmasi.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Kata Sandi') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4 focus:!border-red-500 focus:!ring-red-500"
                    placeholder="{{ __('Kata Sandi') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Hapus Akun') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>