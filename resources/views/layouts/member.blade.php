<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Katalog') — LibraryMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style type="text/tailwindcss">
        .member-surface       { @apply rounded-3xl border border-slate-200 bg-white shadow-sm; }
        .member-surface-soft  { @apply rounded-3xl border border-slate-200 bg-slate-50/80 shadow-sm; }
        .member-pill          { @apply inline-flex items-center rounded-full px-3 py-1 text-xs font-medium; }
        .member-input         { @apply w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100; }
        .member-label         { @apply text-sm font-medium text-slate-700; }
        .member-title         { @apply text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl; }
        .member-lead          { @apply mt-2 max-w-2xl text-sm leading-6 text-slate-500 sm:text-base; }
        .member-primary-btn   { @apply inline-flex items-center justify-center rounded-2xl bg-sky-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-600 hover:shadow-md; }
        .member-secondary-btn { @apply inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-sky-200 hover:text-sky-700 hover:shadow-md; }
        .member-tab           { @apply inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-sm font-medium transition; }
        .member-empty         { @apply rounded-3xl border border-dashed border-slate-200 bg-white px-6 py-14 text-center shadow-sm; }
        .member-stat-card     { @apply rounded-3xl border border-slate-200 bg-white p-5 shadow-sm; }
        .member-stat-icon     { @apply flex h-12 w-12 items-center justify-center rounded-2xl text-xl; }
        .profile-shell        { @apply rounded-[2rem] border border-slate-200 bg-white/90 shadow-sm backdrop-blur; }
        .profile-panel        { @apply rounded-[2rem] border border-slate-200 bg-slate-50/80 p-5 shadow-sm; }
        .profile-kicker       { @apply inline-flex rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700; }
        .profile-section-title{ @apply text-lg font-semibold text-slate-900; }
        .profile-helper       { @apply text-sm leading-6 text-slate-500; }
        .profile-stat         { @apply rounded-2xl border border-slate-200 bg-white p-4 shadow-sm; }
    </style>
    @stack('styles')
</head>
<body class="bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.14),_transparent_34%),linear-gradient(180deg,_#f8fafc_0%,_#f8fafc_42%,_#eff6ff_100%)] font-sans text-slate-800 antialiased">

    <!-- ── Navbar ──────────────────────────────────────────────────────── -->
    <nav class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 shadow-[0_1px_0_rgba(15,23,42,0.02)] backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center gap-4 px-6 py-4">

            {{-- Logo --}}
            <a href="{{ route('member.catalog') }}" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl shadow-sm" style="background:#0EA5E9">
                    <i class="ti ti-books text-lg text-white"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold text-slate-900">LibraryMS</div>
                    <div class="text-xs text-slate-500">Member Portal</div>
                </div>
            </a>

            {{-- Desktop Nav Tabs --}}
            <div class="hidden flex-1 items-center justify-center md:flex">
                <div class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 p-1 shadow-sm">

                    <a href="{{ route('member.catalog') }}"
                        class="member-tab {{ request()->routeIs('member.catalog*') ? 'text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}"
                        style="{{ request()->routeIs('member.catalog*') ? 'background:#0EA5E9' : '' }}">
                        <i class="ti ti-books text-base flex-shrink-0"></i>
                        <span>Katalog</span>
                    </a>

                    <a href="{{ route('member.history') }}"
                        class="member-tab {{ request()->routeIs('member.history*') ? 'text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}"
                        style="{{ request()->routeIs('member.history*') ? 'background:#0EA5E9' : '' }}">
                        <i class="ti ti-clock-hour-4 text-base flex-shrink-0"></i>
                        <span>Riwayat</span>
                    </a>

                    <a href="{{ route('profile.edit') }}"
                        class="member-tab {{ request()->routeIs('profile.edit') ? 'text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}"
                        style="{{ request()->routeIs('profile.edit') ? 'background:#0EA5E9' : '' }}">
                        <i class="ti ti-user-circle text-base flex-shrink-0"></i>
                        <span class="inline-flex items-center gap-2">
                            Profil
                            @if(count(auth()->user()->missingBorrowerProfileFields()) > 0)
                                <span class="h-2 w-2 flex-shrink-0 rounded-full bg-rose-500 shadow-sm {{ request()->routeIs('profile.edit') ? 'ring-2 ring-sky-400' : 'ring-2 ring-white' }}"></span>
                            @endif
                        </span>
                    </a>

                </div>
            </div>

            {{-- User Dropdown --}}
            <div class="relative ml-auto" x-data="{ open: false }" @click.outside="open = false">
                <button
                    type="button"
                    @click="open = !open"
                    class="flex items-center gap-3 rounded-full border border-slate-200 bg-white px-2 py-1.5 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:shadow-md">
                    <x-user-avatar :user="auth()->user()" size="sm" class="border border-sky-100 shadow-sm" />
                    <div class="hidden sm:block">
                        <div class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-500">Member</div>
                    </div>
                    <i class="ti ti-chevron-down text-base text-slate-400 transition" :class="open ? 'rotate-180' : ''"></i>
                </button>

                {{-- Dropdown menu --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-60 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl"
                    style="display:none;">

                    {{-- User info --}}
                    <div class="border-b border-slate-100 px-4 py-3">
                        <div class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</div>
                        <div class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</div>
                    </div>

                    {{-- Links --}}
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-2.5 px-4 py-3 text-sm text-slate-700 transition hover:bg-slate-50 hover:text-sky-700">
                        <i class="ti ti-user-circle text-base text-slate-400"></i>
                        <span>Profil Saya</span>
                        @if(count(auth()->user()->missingBorrowerProfileFields()) > 0)
                            <span class="ml-auto text-xs font-medium text-amber-600">Belum lengkap</span>
                        @endif
                    </a>

                    <a href="{{ route('member.history') }}"
                        class="flex items-center gap-2.5 px-4 py-3 text-sm text-slate-700 transition hover:bg-slate-50 hover:text-sky-700">
                        <i class="ti ti-clock-hour-4 text-base text-slate-400"></i>
                        <span>Riwayat Peminjaman</span>
                    </a>

                    <div class="border-t border-slate-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-2.5 px-4 py-3 text-sm text-red-600 transition hover:bg-red-50">
                                <i class="ti ti-logout text-base"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </nav>

    <!-- ── Flash Messages ──────────────────────────────────────────────── -->
    <div class="mx-auto max-w-7xl px-6 pt-5">
        @if(session('success'))
            <div class="mb-4 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">
                <i class="ti ti-circle-check flex-shrink-0 text-lg text-emerald-500"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
                <i class="ti ti-alert-circle flex-shrink-0 text-lg text-red-500"></i>
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- ── Page Content ────────────────────────────────────────────────── -->
    <main class="mx-auto max-w-7xl px-6 pb-24 pt-2">
        @yield('content')
    </main>

    <!-- ── Mobile Bottom Nav ───────────────────────────────────────────── -->
    <div class="md:hidden fixed bottom-4 left-1/2 z-40 w-[calc(100%-2rem)] max-w-md -translate-x-1/2 rounded-3xl border border-slate-200 bg-white/95 p-2 shadow-xl backdrop-blur-xl">
        <div class="grid grid-cols-3 gap-1">

            <a href="{{ route('member.catalog') }}"
                class="flex flex-col items-center gap-1 rounded-2xl px-3 py-2.5 text-xs font-medium transition
                    {{ request()->routeIs('member.catalog*') ? 'bg-sky-50 text-sky-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                <i class="ti ti-books text-xl {{ request()->routeIs('member.catalog*') ? 'text-sky-500' : '' }}"></i>
                <span>Katalog</span>
            </a>

            <a href="{{ route('member.history') }}"
                class="flex flex-col items-center gap-1 rounded-2xl px-3 py-2.5 text-xs font-medium transition
                    {{ request()->routeIs('member.history*') ? 'bg-sky-50 text-sky-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                <i class="ti ti-clock-hour-4 text-xl {{ request()->routeIs('member.history*') ? 'text-sky-500' : '' }}"></i>
                <span>Riwayat</span>
            </a>

            <a href="{{ route('profile.edit') }}"
                class="relative flex flex-col items-center gap-1 rounded-2xl px-3 py-2.5 text-xs font-medium transition
                    {{ request()->routeIs('profile.edit') ? 'bg-sky-50 text-sky-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                <span class="relative">
                    <i class="ti ti-user-circle text-xl {{ request()->routeIs('profile.edit') ? 'text-sky-500' : '' }}"></i>
                    @if(count(auth()->user()->missingBorrowerProfileFields()) > 0)
                        <span class="absolute -right-1 -top-1 h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                    @endif
                </span>
                <span>Profil</span>
            </a>

        </div>
    </div>

    @stack('scripts')
</body>
</html>