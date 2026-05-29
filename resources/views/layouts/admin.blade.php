<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — LibraryMS</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tabler Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sky: {
                            primary: '#0EA5E9',
                            dark: '#0284C7',
                            light: '#E0F2FE',
                        }
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        .sidebar-link {@apply flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/75 text-sm transition-all duration-150; }
        .sidebar-link:hover { @apply bg-white/10 text-white; }
        .sidebar-link.active { @apply bg-white/20 text-white font-medium; }
        .sidebar-label { @apply text-white/50 text-xs font-medium uppercase tracking-wider px-3 mt-4 mb-1; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 font-sans">
<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-56 bg-sky-500 flex flex-col fixed top-0 left-0 h-full z-30 shadow-lg" style="background:#0EA5E9">
        <!-- Logo -->
        <div class="px-5 py-5 border-b border-white/15">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="ti ti-books text-white text-xl"></i>
                </div>
                <div>
                    <div class="text-white font-semibold text-sm leading-tight">LibraryMS</div>
                    <div class="text-white/60 text-xs">Management System</div>
                </div>
            </div>
        </div>

        <!-- Nav -->
        
        <nav class="flex-1 px-3 py-3 overflow-y-auto">
            <div class="sidebar-label">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard text-lg"></i> Dashboard
            </a>
            <a href="{{ route('admin.books.index') }}" class="sidebar-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                <i class="ti ti-books text-lg"></i> Kelola Buku
            </a>
            <a href="{{ route('admin.members.index') }}" class="sidebar-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                <i class="ti ti-users text-lg"></i> Anggota
            </a>
            <a href="{{ route('admin.borrowings.index') }}" class="sidebar-link {{ request()->routeIs('admin.borrowings.*') ? 'active' : '' }}">
                <i class="ti ti-clipboard-list text-lg"></i> Peminjaman
            </a>
            <div class="sidebar-label">Lainnya</div>
            <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="ti ti-category text-lg"></i> Kategori
            </a>
            <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="ti ti-chart-bar text-lg"></i> Laporan
            </a>
            <a href="{{ route('admin.settings') }}" class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="ti ti-settings text-lg"></i> Pengaturan
            </a>
        </nav>

        <!-- User -->
        <div class="px-3 py-3 border-t border-white/15">
            <div class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-white/10 cursor-pointer">
                <div class="w-8 h-8 rounded-full bg-white/25 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-white text-xs font-medium truncate">{{ auth()->user()->name }}</div>
                    <div class="text-white/60 text-xs truncate">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="sidebar-link w-full text-left">
                    <i class="ti ti-logout text-lg"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 ml-56 flex flex-col min-h-screen">
        <!-- Topbar -->
        <header class="bg-white border-b border-slate-200 px-6 py-3.5 flex items-center justify-between sticky top-0 z-20">
            <div>
                <h1 class="text-base font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-slate-500 mt-0.5">@yield('page-subtitle', 'Selamat datang kembali, ' . auth()->user()->name)</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="w-9 h-9 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50">
                    <i class="ti ti-search text-lg"></i>
                </button>
                <button class="w-9 h-9 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 relative">
                    <i class="ti ti-bell text-lg"></i>
                </button>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-6 pt-4">
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

        <!-- Page Content -->
        <main class="flex-1 px-6 pb-8">
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>