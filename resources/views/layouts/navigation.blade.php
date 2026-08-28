<!-- Mobile backdrop -->
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-[#180733]/50 z-40 lg:hidden"
     style="display: none;"></div>

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col text-white transition-transform duration-200 ease-in-out lg:translate-x-0"
    style="background: linear-gradient(180deg,#2A0B5E 0%,#180733 100%)"
>
    @php
        // Diambil dari Spatie Laravel-Permission (HasRoles).
        $roleName = Auth::user()->getRoleNames()->first();
        $roleLabel = match ($roleName) {
            'dinkes' => 'Superadmin',
            'admin-puskesmas', 'dinkes-skm' => 'Admin',
            'petugas' => 'Petugas',
            default => $roleName ? ucfirst(str_replace(['_', '-'], ' ', $roleName)) : 'Superadmin',
        };
    @endphp

    <!-- Brand -->
    <div class="flex items-center justify-between gap-3 px-6 pt-6 pb-5">
        <a href="{{ route('dinkes.dashboard') }}" class="flex items-center gap-3 min-w-0">
            <x-application-logo class="w-9 h-9 fill-current text-white shrink-0" />
            <span class="min-w-0">
                <span class="block font-extrabold text-[1.02rem] leading-tight truncate">SKM</span>
                <span class="block text-[0.72rem] text-white/50 font-semibold truncate">{{ $roleLabel }}</span>
            </span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-white/60 hover:text-white">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Nav links -->
    <nav class="flex-1 overflow-y-auto px-3.5 py-2 space-y-0.5">
        <a href="{{ route('dinkes.dashboard') }}"
           class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition
                  {{ request()->routeIs('dinkes.dashboard') ? 'bg-white/[.12] text-white shadow-[inset_3px_0_0_#E4A63B]' : 'text-white/70 hover:bg-white/[.08] hover:text-white' }}">
            <i class="fa-solid fa-house w-[18px] text-center"></i>
            {{ __('Dashboard') }}
        </a>

        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition
                  {{ request()->routeIs('profile.edit') ? 'bg-white/[.12] text-white shadow-[inset_3px_0_0_#E4A63B]' : 'text-white/70 hover:bg-white/[.08] hover:text-white' }}">
            <i class="fa-solid fa-user-gear w-[18px] text-center"></i>
            {{ __('Profile') }}
        </a>
    </nav>

    <!-- Logout -->
    <div class="px-3.5 pb-4 pt-2 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); this.closest('form').submit();"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold text-white/70 hover:bg-white/[.08] hover:text-white transition cursor-pointer">
                <i class="fa-solid fa-right-from-bracket w-[18px] text-center"></i>
                {{ __('Log Out') }}
            </a>
        </form>
    </div>

    <div class="px-6 pb-5 text-[0.7rem] text-white/35">
        &copy; {{ date('Y') }} SKM
    </div>
</aside>