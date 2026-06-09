<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — LibraryMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body class="font-sans">
<div class="flex min-h-screen">

    <!-- Left Panel -->
    <div class="hidden lg:flex lg:w-5/12 flex-col justify-between p-10 text-white" style="background:#0EA5E9">
        <div>
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="ti ti-books text-white text-2xl"></i>
                </div>
                <span class="font-semibold text-lg">LibraryMS</span>
            </div>
            <h2 class="text-3xl font-bold leading-tight mb-4">Reset password Anda dengan aman</h2>
            <p class="text-white/75 text-sm leading-relaxed">Masukkan password baru untuk akun Anda. Setelah reset, Anda bisa masuk kembali menggunakan password baru tersebut.</p>
        </div>
        <div class="text-white/50 text-xs">© 2026 LibraryMS. All rights reserved.</div>
    </div>

    <!-- Right Panel -->
    <div class="flex-1 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-md">
            <div class="flex bg-slate-100 rounded-xl p-1 mb-8">
                <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 transition-all">Masuk</a>
                <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-800 transition-all">Daftar</a>
                <span class="flex-1 text-center py-2.5 rounded-lg text-sm font-medium text-white bg-slate-800">Reset Password</span>
            </div>

            <h2 class="text-2xl font-bold text-slate-800 mb-1">Reset Password</h2>
            <p class="text-slate-500 text-sm mb-6">Masukkan email dan password baru Anda untuk menyelesaikan proses reset password.</p>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="nama@email.com"
                        class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition-all @error('email') border-red-400 bg-red-50 @else border-slate-200 focus:border-sky-400 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password Baru</label>
                    <input type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter"
                        class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition-all @error('password') border-red-400 bg-red-50 @else border-slate-200 focus:border-sky-400 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru"
                        class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition-all @error('password_confirmation') border-red-400 bg-red-50 @else border-slate-200 focus:border-sky-400 @enderror">
                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-3 rounded-xl text-white font-medium text-sm transition-all hover:opacity-90" style="background:#0EA5E9">Reset Password</button>
            </form>

            <div class="mt-6 text-sm text-slate-500">
                <p>Sudah ingat password? <a href="{{ route('login') }}" class="font-medium" style="color:#0EA5E9">Masuk kembali</a></p>
                <p class="mt-2">Belum punya akun? <a href="{{ route('register') }}" class="font-medium" style="color:#0EA5E9">Daftar sekarang</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
