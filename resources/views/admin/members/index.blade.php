@extends('layouts.admin')
@section('title', 'Kelola Anggota')
@section('page-title', 'Kelola Anggota')
@section('page-subtitle', 'Manajemen data anggota perpustakaan')

@section('content')
<div class="pt-2">
    <div class="flex items-center justify-between mb-5">
        <div></div>
        <a href="{{ route('admin.members.create') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-medium hover:opacity-90" style="background:#0EA5E9">
            <i class="ti ti-user-plus text-base"></i> Tambah Anggota
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 mb-5 flex items-center gap-3">
        <div class="flex-1 relative">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
        </div>
        <select name="status" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none text-slate-600">
            <option value="">Semua</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
        </select>
        <button type="submit" class="px-4 py-2.5 rounded-xl border text-sm font-medium" style="border-color:#0EA5E9;color:#0EA5E9">Filter</button>
    </form>

    <!-- Cards Grid -->
    <div class="grid grid-cols-3 gap-4">
        @forelse($members as $member)
        <div class="bg-white rounded-xl border border-slate-200 p-5 hover:border-sky-200 transition-all">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <x-user-avatar :user="$member" size="md" class="flex-shrink-0" />
                    <div>
                        <div class="text-sm font-semibold text-slate-800">{{ $member->name }}</div>
                        <div class="text-xs text-slate-500">{{ $member->email }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $member->identity_number ?? 'NIK belum diisi' }}</div>
                    </div>
                </div>
                @if($member->borrowings_count > 0)
                    <span class="text-xs px-2 py-1 rounded-full font-medium" style="background:#DCFCE7;color:#15803D">Aktif</span>
                @else
                    <span class="text-xs px-2 py-1 rounded-full font-medium" style="background:#FEE2E2;color:#B91C1C">Nonaktif</span>
                @endif
            </div>
            <div class="flex items-center justify-between text-xs text-slate-500 mb-4 py-3 border-t border-b border-slate-100">
                <div>
                    <div class="text-slate-400">Bergabung</div>
                    <div class="font-medium text-slate-700 mt-0.5">{{ optional($member->created_at)->format('d M Y') ?? 'Belum diisi' }}</div>
                </div>
                <div class="text-right">
                    <div class="text-slate-400">Total Pinjaman</div>
                    <div class="font-semibold mt-0.5" style="color:#0EA5E9">{{ $member->borrowings_count }} buku</div>
                </div>
            </div>
            <div class="mb-4 text-xs">
                @if($member->hasBorrowerProfile())
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full" style="background:#DCFCE7;color:#15803D">
                        <i class="ti ti-check text-xs"></i> Profil lengkap
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full" style="background:#FEF3C7;color:#B45309">
                        <i class="ti ti-alert-circle text-xs"></i> Profil perlu dilengkapi
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.members.show', ['member' => $member->id]) }}" class="flex-1 text-center py-2 rounded-lg border text-xs font-medium transition-all hover:bg-sky-50" style="border-color:#0EA5E9;color:#0EA5E9">
                    <i class="ti ti-eye mr-1"></i> Lihat Detail
                </a>
                <a href="{{ route('admin.members.edit', ['member' => $member->id]) }}" class="flex-1 text-center py-2 rounded-lg border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-all">
                    <i class="ti ti-pencil mr-1"></i> Edit
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12 text-slate-400">
            <i class="ti ti-users text-4xl block mb-2"></i>
            <div class="text-sm">Belum ada anggota</div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($members->hasPages())
    <div class="mt-5 flex items-center justify-between">
        <div class="text-sm text-slate-500">Menampilkan {{ $members->firstItem() }} dari {{ $members->total() }} anggota</div>
        {{ $members->links() }}
    </div>
    @endif
</div>
@endsection