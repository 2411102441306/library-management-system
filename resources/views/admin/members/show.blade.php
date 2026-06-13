{{-- resources/views/admin/members/show.blade.php --}}
@extends('layouts.admin')
@section('title', 'Detail Anggota')
@section('page-title', 'Detail Anggota')
@section('page-subtitle', $user->name)

@section('content')
<div class="pt-2 max-w-6xl space-y-6">
    <div class="grid gap-6 lg:grid-cols-[360px_1fr]">
        <!-- Profile Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="text-center">
                <x-user-avatar :user="$user" size="xl" class="mx-auto mb-4 border border-slate-100 shadow-sm" />
                <div class="text-lg font-semibold text-slate-900">{{ $user->name }}</div>
                <div class="mt-1 text-sm text-slate-500">{{ $user->email }}</div>
                <div class="mt-1 text-xs text-slate-400">{{ $user->identity_number ?? 'NIK belum diisi' }}</div>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                    <div class="text-xs uppercase tracking-wide text-slate-400">Nomor telepon</div>
                    <div class="mt-1 text-sm font-medium text-slate-700">{{ $user->phone ?? 'Belum diisi' }}</div>
                </div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                    <div class="text-xs uppercase tracking-wide text-slate-400">Tempat dan tanggal lahir</div>
                    <div class="mt-1 text-sm font-medium text-slate-700">{{ $user->birth_place ?? 'Belum diisi' }}{{ $user->birth_date ? ', ' . $user->birth_date->format('d M Y') : '' }}</div>
                </div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3 sm:col-span-2 lg:col-span-1">
                    <div class="text-xs uppercase tracking-wide text-slate-400">Alamat</div>
                    <div class="mt-1 text-sm font-medium leading-6 text-slate-700">{{ $user->address ?? 'Belum diisi' }}</div>
                </div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                    <div class="text-xs uppercase tracking-wide text-slate-400">Foto profil</div>
                    <div class="mt-1 text-sm font-medium text-slate-700">{{ $user->profile_photo_path ? 'Tersedia' : 'Belum diisi' }}</div>
                </div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                    <div class="text-xs uppercase tracking-wide text-slate-400">Bergabung</div>
                    <div class="mt-1 text-sm font-medium text-slate-700">{{ optional($user->created_at)->format('d M Y') ?? 'Belum diisi' }}</div>
                </div>
            </div>

            <div class="mt-5">
                @if($user->hasBorrowerProfile())
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                        <i class="ti ti-check text-xs"></i> Profil lengkap
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                        <i class="ti ti-alert-circle text-xs"></i> Profil belum lengkap
                    </span>
                @endif
            </div>

            <div class="mt-5 flex gap-2">
                <a href="{{ route('admin.members.edit', ['member' => $user->id]) }}" class="member-primary-btn flex-1">
                    Edit Profil
                </a>
            </div>
        </div>

        <!-- Borrowing History -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Riwayat Peminjaman</h3>
                    <p class="mt-1 text-xs text-slate-500">Lima peminjaman terakhir untuk verifikasi aktivitas anggota.</p>
                </div>
                <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-700">{{ $borrowings->count() }} data</span>
            </div>
            @forelse($borrowings as $borrow)
            <div class="flex items-center gap-3 rounded-2xl border border-slate-100 px-4 py-3 transition last:mb-0 last:border-0 last:px-0 last:py-0">
                <div class="flex h-11 w-9 flex-shrink-0 items-center justify-center rounded-2xl bg-sky-50">
                    <i class="ti ti-book text-sm text-sky-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-slate-900 truncate">{{ $borrow->book->title }}</div>
                    <div class="text-xs text-slate-500">{{ optional($borrow->borrow_date)->format('d M Y') ?? '-' }} — {{ optional($borrow->due_date)->format('d M Y') ?? '-' }}</div>
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
                    <span class="rounded-full px-2.5 py-1 text-xs font-medium" style="background:{{ $cfg[0] }};color:{{ $cfg[1] }}">{{ $cfg[2] }}</span>
            </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 py-10 text-center text-sm text-slate-400">Belum ada riwayat peminjaman</div>
            @endforelse
        </div>
    </div>
        <div>
            <a href="{{ route('admin.members.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700">
            <i class="ti ti-arrow-left text-base"></i> Kembali ke daftar anggota
        </a>
    </div>
</div>
@endsection