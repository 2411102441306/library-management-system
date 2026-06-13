{{-- Simpan sebagai: resources/views/admin/members/create.blade.php --}}
@extends('layouts.admin')
@section('title', 'Tambah Anggota')
@section('page-title', 'Tambah Anggota')
@section('page-subtitle', 'Daftarkan anggota baru')

@section('content')
<div class="pt-2">
    {{-- Form membungkus seluruh grid agar tombol di kanan tetap bisa men-submit data --}}
    <form method="POST" action="{{ route('admin.members.store') }}">
        @csrf
        
        {{-- Menggunakan Grid 3 Kolom pada desktop (lg) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm lg:col-span-2">
                <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i class="ti ti-user text-base" style="color:#0EA5E9"></i>
                    Informasi Pribadi
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap anggota"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all @error('name') border-red-400 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email <span class="text-red-400">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all @error('email') border-red-400 @enderror">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">No. Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xx"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">NIK / Nomor Identitas</label>
                        <input type="text" name="identity_number" value="{{ old('identity_number') }}" placeholder="Nomor identitas unik"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tempat Lahir</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place') }}" placeholder="Samarinda"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
                    </div>
                    
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat</label>
                        <textarea name="address" rows="4" placeholder="Alamat lengkap (opsional)"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 resize-none transition-all">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm lg:col-span-1">
                <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i class="ti ti-shield-lock text-base" style="color:#0EA5E9"></i>
                    Keamanan Akun
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Password <span class="text-red-400">*</span></label>
                        <input type="password" name="password" placeholder="Min. 8 karakter"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all @error('password') border-red-400 @enderror">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password <span class="text-red-400">*</span></label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
                    </div>
                </div>

                <div class="flex flex-col gap-2 mt-6 pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full px-6 py-2.5 rounded-xl text-white text-sm font-medium hover:opacity-90 flex items-center justify-center gap-2 transition-all" style="background:#0EA5E9">
                        <i class="ti ti-user-plus"></i> Tambah Anggota
                    </button>
                    <a href="{{ route('admin.members.index') }}" class="w-full px-6 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-all text-center">
                        Batal
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection