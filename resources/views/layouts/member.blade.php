<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Katalog') — LibraryMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @stack('styles')
</head>
<body class="bg-slate-50 font-sans">

    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-sky-500 rounded-lg flex items-center justify-center" style="background:#0EA5E9">
                    <i class="ti ti-books text-white text-base"></i>
                </div>
                <span class="font-semibold text-slate-800 text-sm">LibraryMS</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('member.catalog') }}" class="text-sm {{ request()->routeIs('member.catalog*') ? 'text-sky-500 font-medium' : 'text-slate-600 hover:text-sky-500' }}" style="{{ request()->routeIs('member.catalog*') ? 'color:#0EA5E9' : '' }}">
                    Katalog
                </a>
                <a href="{{ route('member.history') }}" class="text-sm {{ request()->routeIs('member.history*') ? 'text-sky-500 font-medium' : 'text-slate-600 hover:text-sky-500' }}" style="{{ request()->routeIs('member.history*') ? 'color:#0EA5E9' : '' }}">
                    Riwayat Saya
                </a>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-semibold" style="background:#0EA5E9">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <span class="text-sm text-slate-700 font-medium">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-slate-500 hover:text-red-500 flex items-center gap-1">
                        <i class="ti ti-logout text-base"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-6 pt-4">
        @if(session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">
                <i class="ti ti-circle-check text-green-500 text-lg flex-shrink-0"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4 text-sm">
                <i class="ti ti-alert-circle text-red-500 text-lg flex-shrink-0"></i>
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Content -->
    <main class="max-w-7xl mx-auto px-6 py-6">
        @yield('content')
    </main>

@stack('scripts')
</body>
</html>