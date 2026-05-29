{{-- resources/views/admin/members/show.blade.php --}}
@extends('layouts.admin')
@section('title', 'Detail Anggota')
@section('page-title', 'Detail Anggota')
@section('page-subtitle', $user->name)

@section('content')
<div class="pt-2 max-w-3xl">
    <div class="grid grid-cols-3 gap-4">
        <!-- Profile Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 col-span-1">
            <div class="text-center mb-4">
                <div class="w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-3" style="background:#E0F2FE;color:#0EA5E9">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="text-sm font-semibold text-slate-800">{{ $user->name }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $user->email }}</div>
            </div>
            <div class="space-y-2 text-xs">
                <div class="flex items-center gap-2 text-slate-600">
                    <i class="ti ti-phone text-slate-400"></i>
                    {{ $user->phone ?? 'Belum diisi' }}
                </div>
                <div class="flex items-center gap-2 text-slate-600">
                    <i class="ti ti-calendar text-slate-400"></i>
                    Bergabung {{ $user->created_at->format('d M Y') }}
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('admin.members.edit', $user) }}" class="flex-1 text-center py-2 rounded-lg text-xs font-medium text-white hover:opacity-90" style="background:#0EA5E9">
                    Edit Profil
                </a>
            </div>
        </div>

        <!-- Borrowing History -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 col-span-2">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Riwayat Peminjaman</h3>
            @forelse($borrowings as $borrow)
            <div class="flex items-center gap-3 py-2.5 border-b border-slate-100 last:border-0">
                <div class="w-8 h-10 rounded flex items-center justify-center flex-shrink-0" style="background:#E0F2FE">
                    <i class="ti ti-book text-sm" style="color:#0EA5E9"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-slate-800 truncate">{{ $borrow->book->title }}</div>
                    <div class="text-xs text-slate-500">{{ $borrow->borrow_date->format('d M Y') }} — {{ $borrow->due_date->format('d M Y') }}</div>
                </div>
                @php
                    $cfg = match($borrow->status) {
                        'approved' => ['#DCFCE7','#15803D','Aktif'],
                        'pending'  => ['#FEF3C7','#B45309','Menunggu'],
                        'overdue'  => ['#FEE2E2','#B91C1C','Terlambat'],
                        'returned' => ['#DBEAFE','#1D4ED8','Selesai'],
                        default    => ['#F3F4F6','#6B7280','Ditolak'],
                    };
                @endphp
                <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background:{{ $cfg[0] }};color:{{ $cfg[1] }}">{{ $cfg[2] }}</span>
            </div>
            @empty
            <div class="text-center py-6 text-slate-400 text-sm">Belum ada riwayat peminjaman</div>
            @endforelse
        </div>
    </div>
    <div class="mt-4">
        <a href="{{ route('admin.members.index') }}" class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1">
            <i class="ti ti-arrow-left text-base"></i> Kembali ke daftar anggota
        </a>
    </div>
</div>
@endsection