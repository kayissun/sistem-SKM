@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-200 focus:border-violet-600 focus:ring-violet-600 rounded-lg shadow-sm text-sm placeholder:text-slate-400']) }}>