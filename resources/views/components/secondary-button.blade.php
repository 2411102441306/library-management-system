<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-xs font-semibold uppercase tracking-widest text-slate-700 shadow-sm transition hover:border-sky-200 hover:text-sky-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-sky-100 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
