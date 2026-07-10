@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full border border-slate-200 bg-white/80 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 rounded-xl shadow-sm px-4 py-3 text-slate-800 placeholder-slate-400 transition-all duration-200 outline-none']) }}>
