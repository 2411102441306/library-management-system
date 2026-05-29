<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — LibraryMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body class="font-sans">
<div class="flex min-h-screen">

    <!-- Left Panel -->
    <div class="hidden lg:flex lg:w-5/12 flex-col justify-between p-10 text-white" style="background:#0EA5E9">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="ti ti-books text-white text-2xl"></i>
            </div>
            <span class="font-semibold text-lg">LibraryMS</span>
        </div>
        <div>
            <h2 class="text-3xl font-bold leading-tight mb-4">Kelola Perpustakaan<br>Digital Anda</h2>
            <p class="text-white/75 text-sm leading-relaxed mb-8">Platform manajemen perpustakaan yang lengkap, mudah digunakan, dan modern untuk institusi Anda.</p>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white/15 rounded-xl p-4">
                    <div class="text-white/70 text-xs mb-1">Total Buku</div>
                    <div class="text-2xl font-bold">{{ number_format($totalBooks ?? 0) }}</div>
                </div>
                <div class="bg-white/15 rounded-xl p-4">
                    <div class="text-white/70 text-xs mb-1">Anggota Aktif</div>
                    <div class="text-2xl font-bold">{{ number_format($activeMembers ?? 0) }}</div>
                </div>
                <div class="bg-white/15 rounded-xl p-4">
                    <div class="text-white/70 text-xs mb-1">Dipinjam</div>
                    <div class="text-2xl font-bold">{{ number_format($borrowed ?? 0) }}</div>
                </div>
                <div class="bg-white/15 rounded-xl p-4">
                    <div class="text-white/70 text-xs mb-1">Kategori</div>
                    <div class="text-2xl font-bold">{{ number_format($categories ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="text-white/50 text-xs">© 2026 LibraryMS. All rights reserved.</div>
    </div>

    <!-- Right Panel -->
    <div class="flex-1 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-md">
            <!-- Tab -->
            <div class="flex bg-slate-100 rounded-xl p-1 mb-8">
                <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 rounded-lg text-sm font-medium transition-all" style="background:#0EA5E9;color:white">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-700 transition-all">
                    Daftar
                </a>
            </div>

            <h2 class="text-2xl font-bold text-slate-800 mb-1">Masuk ke Akun Anda</h2>
            <p class="text-slate-500 text-sm mb-6">Silakan masukkan email dan password Anda</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com"
                        class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition-all @error('email') border-red-400 bg-red-50 @else border-slate-200 focus:border-sky-400 @enderror"
                        style="focus:border-color:#0EA5E9">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="Masukkan password"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none transition-all focus:border-sky-400 pr-10 @error('password') border-red-400 bg-red-50 @enderror">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="ti ti-eye text-lg" id="eye-icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300" style="accent-color:#0EA5E9">
                        <span class="text-sm text-slate-600">Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium" style="color:#0EA5E9">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="w-full py-3 rounded-xl text-white font-medium text-sm transition-all hover:opacity-90" style="background:#0EA5E9">
                    Masuk
                </button>

                <p class="text-center text-sm text-slate-500 mt-5">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-medium" style="color:#0EA5E9">Daftar sekarang</a>
                </p>
            </form>
        </div>
    </div>
</div>
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ti ti-eye-off text-lg';
    } else {
        input.type = 'password';
        icon.className = 'ti ti-eye text-lg';
    }
}
</script>
</body>
</html>