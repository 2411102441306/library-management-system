@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Konfigurasi akun administrator dan aturan peminjaman')

@section('content')
{{-- Mengubah container menjadi grid 2 kolom pada layar besar (lg) --}}
<div class="pt-2 grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">

    @if($errors->any())
    <div class="xl:col-span-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        <div class="font-semibold mb-1">Ada data yang perlu diperbaiki.</div>
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
            <i class="ti ti-user-circle text-base" style="color:#0EA5E9"></i>
            Profil Admin
        </h3>
        <div class="flex items-center gap-4 mb-5 pb-5 border-b border-slate-100">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-lg font-bold flex-shrink-0" style="background:#E0F2FE;color:#0EA5E9">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div>
                <div class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
                <div class="text-xs mt-1 px-2 py-0.5 rounded-full inline-block" style="background:#E0F2FE;color:#0369A1">Administrator</div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf @method('PUT')
            <input type="hidden" name="profile_section" value="1">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
            </div>
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
            </div>
            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-white text-sm font-medium hover:opacity-90 flex items-center justify-center gap-2 transition-all" style="background:#0EA5E9">
                <i class="ti ti-device-floppy"></i> Simpan Perubahan
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
            <i class="ti ti-bookmark text-base" style="color:#0EA5E9"></i>
            Aturan Peminjaman
        </h3>
        <p class="text-xs text-slate-500 mb-4 leading-5">
            Aturan ini dipakai saat member mengajukan pinjaman dan saat sistem menghitung keterlambatan.
        </p>
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf @method('PUT')
            <input type="hidden" name="policy_section" value="1">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Durasi Default</label>
                    <input type="number" name="default_days" value="{{ old('default_days', $policy['default_days']) }}" min="1" max="30"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Denda / Hari</label>
                    <input type="number" name="daily_fine" value="{{ old('daily_fine', $policy['daily_fine']) }}" min="0"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Denda Hilang</label>
                    <input type="number" name="lost_fee" value="{{ old('lost_fee', $policy['lost_fee']) }}" min="0"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Durasi Minimum</label>
                    <input type="number" name="min_days" value="{{ old('min_days', $policy['min_days']) }}" min="1" max="30"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Durasi Maksimum</label>
                    <input type="number" name="max_days" value="{{ old('max_days', $policy['max_days']) }}" min="1" max="30"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
                </div>
            </div>
            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-white text-sm font-medium hover:opacity-90 flex items-center justify-center gap-2 transition-all" style="background:#0EA5E9">
                <i class="ti ti-device-floppy"></i> Simpan Aturan
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
            <i class="ti ti-lock text-base" style="color:#0EA5E9"></i>
            Keamanan
        </h3>
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf @method('PUT')
            <input type="hidden" name="security_section" value="1">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Password Lama</label>
                <input type="password" name="current_password" placeholder="Masukkan password lama"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Password Baru</label>
                <input type="password" name="password" placeholder="Min. 8 karakter"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
            </div>
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
            </div>
            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border text-sm font-medium flex items-center justify-center gap-2 hover:bg-sky-50 transition-all" style="border-color:#0EA5E9;color:#0EA5E9">
                <i class="ti ti-lock-check"></i> Ubah Password
            </button>
        </form>
    </div>

</div>
@endsection