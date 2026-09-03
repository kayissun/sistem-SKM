<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#6D28D9] rounded-lg font-semibold text-sm text-white shadow-[0_10px_20px_-6px_rgba(46,16,101,0.45)] focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 disabled:opacity-50']) }}>
    {{ $slot }}
</button>