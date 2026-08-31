@php
    // Diambil dari Spatie Laravel-Permission (HasRoles).
    $roleName = $user->getRoleNames()->first();
    $roleLabel = match ($roleName) {
        'dinkes' => 'Superadmin',
        'admin-puskesmas', 'dinkes-skm' => 'Admin',
        default => $roleName ? ucfirst(str_replace(['_', '-'], ' ', $roleName)) : 'Tidak diketahui',
    };

    // puskesmas_id dipakai untuk 2 relasi berbeda (puskesmas() & instansi()) di model User —
    // pakai yang ada isinya.
    $unitNama = $user->puskesmas->nama ?? $user->instansi->nama ?? null;
@endphp

<section>
    <header class="flex items-start gap-3 mb-5">
        <div class="w-10 h-10 rounded-xl bg-[#EDE9FE] text-[#6D28D9] flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-id-badge"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-[#180733]">
                {{ __('Peran & Unit') }}
            </h2>
            <p class="mt-1 text-sm text-[#625B78]">
                {{ __('Informasi akses akun kamu di sistem. Hubungi Dinkes jika ada yang perlu diubah.') }}
            </p>
        </div>
    </header>

    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="p-4 rounded-xl bg-[#FAF8FF] border border-[#180733]/[0.06]">
            <dt class="text-[11px] font-bold uppercase tracking-wide text-[#625B78]">{{ __('Peran') }}</dt>
            <dd class="mt-1.5">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-[#EDE9FE] text-[#6D28D9]">
                    {{ $roleLabel }}
                </span>
            </dd>
        </div>

        <div class="p-4 rounded-xl bg-[#FAF8FF] border border-[#180733]/[0.06]">
            <dt class="text-[11px] font-bold uppercase tracking-wide text-[#625B78]">{{ __('Nama Unit') }}</dt>
            <dd class="mt-1.5 text-sm font-bold text-[#180733]">
                {{ $unitNama ?? __('— (akun Dinkes, tidak terikat satu unit)') }}
            </dd>
        </div>
    </dl>
</section>