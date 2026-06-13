<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-2xl border border-transparent bg-sky-500 px-5 py-3 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-sky-600 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-sky-100 active:bg-sky-700']) }}>
    {{ $slot }}
</button>
