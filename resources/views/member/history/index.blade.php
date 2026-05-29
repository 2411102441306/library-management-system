@extends('layouts.member')
@section('title', 'Riwayat Peminjaman')

@section('content')

    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Riwayat Peminjaman Saya</h1>
        <p class="text-slate-500 text-sm mt-0.5">Pantau status peminjaman buku kamu</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#E0F2FE">
                <i class="ti ti-clipboard-list text-xl" style="color:#0EA5E9"></i>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-800">{{ $counts['all'] }}</div>
                <div class="text-xs text-slate-500">Total Dipinjam</div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#DCFCE7">
                <i class="ti ti-clock text-xl" style="color:#22C55E"></i>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-800">{{ $counts['approved'] }}</div>
                <div class="text-xs text-slate-500">Sedang Dipinjam</div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#FEE2E2">
                <i class="ti ti-alert-circle text-xl" style="color:#EF4444"></i>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-800">{{ $counts['overdue'] }}</div>
                <div class="text-xs text-slate-500">Terlambat</div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex items-center gap-1 mb-5 bg-white border border-slate-200 rounded-xl p-1 w-fit">
        @php
            $tabs = [
                'all'      => ['label' => 'Semua',    'count' => $counts['all']],
                'pending'  => ['label' => 'Menunggu', 'count' => $counts['pending']],
                'approved' => ['label' => 'Aktif',    'count' => $counts['approved']],
                'overdue'  => ['label' => 'Terlambat','count' => $counts['overdue']],
                'returned' => ['label' => 'Selesai',  'count' => $counts['returned']],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
        <a href="{{ route('member.history', ['status' => $key]) }}"
            class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-all"
            style="{{ request('status', 'all') === $key ? 'background:#0EA5E9;color:white' : 'color:#64748B' }}">
            {{ $tab['label'] }}
            @if($tab['count'] > 0)
            <span class="text-xs px-1.5 py-0.5 rounded-full" style="{{ request('status', 'all') === $key ? 'background:rgba(255,255,255,0.2);color:white' : 'background:#F1F5F9;color:#64748B' }}">
                {{ $tab['count'] }}
            </span>
            @endif
        </a>
        @endforeach
    </div>

    <!-- Borrowing Cards -->
    <div class="space-y-3">
        @forelse($borrowings as $borrow)
        @php
            $isOverdue = $borrow->status === 'overdue';
            $cfg = match($borrow->status) {
                'approved' => ['#DCFCE7','#15803D','Aktif'],
                'pending'  => ['#FEF3C7','#B45309','Menunggu Persetujuan'],
                'overdue'  => ['#FEE2E2','#B91C1C','Terlambat'],
                'returned' => ['#DBEAFE','#1D4ED8','Selesai'],
                default    => ['#F3F4F6','#6B7280','Ditolak'],
            };
        @endphp
        <div class="bg-white rounded-xl border overflow-hidden transition-all {{ $isOverdue ? 'border-red-300' : 'border-slate-200' }}"
            style="{{ $isOverdue ? 'border-left: 4px solid #EF4444' : '' }}">
            <div class="flex items-center gap-4 p-4">
                <!-- Cover -->
                <div class="w-12 h-16 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden" style="background:#F0F9FF">
                    @if($borrow->book->cover_image)
                        <img src="{{ $borrow->book->cover_url }}" class="w-full h-full object-cover">
                    @else
                        <i class="ti ti-book text-2xl" style="color:#BAE6FD"></i>
                    @endif
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-800">{{ $borrow->book->title }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $borrow->book->author }}</p>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium flex-shrink-0" style="background:{{ $cfg[0] }};color:{{ $cfg[1] }}">
                            {{ $cfg[2] }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 mt-2 text-xs text-slate-500">
                        <span class="flex items-center gap-1">
                            <i class="ti ti-calendar text-xs"></i>
                            Dipinjam: {{ $borrow->borrow_date->format('d M Y') }}
                        </span>
                        <span class="flex items-center gap-1 {{ $isOverdue ? 'font-semibold' : '' }}" style="{{ $isOverdue ? 'color:#EF4444' : '' }}">
                            <i class="ti ti-clock text-xs"></i>
                            Jatuh tempo: {{ $borrow->due_date->format('d M Y') }}
                            @if($isOverdue)
                                ({{ $borrow->days_late }} hari terlambat)
                            @endif
                        </span>
                        @if($borrow->return_date)
                        <span class="flex items-center gap-1">
                            <i class="ti ti-check text-xs"></i>
                            Dikembalikan: {{ $borrow->return_date->format('d M Y') }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16 bg-white rounded-xl border border-slate-200">
            <i class="ti ti-clipboard-list text-5xl block mb-3 text-slate-300"></i>
            <div class="text-base font-medium text-slate-500">Belum ada riwayat peminjaman</div>
            <p class="text-sm text-slate-400 mt-1 mb-4">Mulai jelajahi koleksi buku kami</p>
            <a href="{{ route('member.catalog') }}" class="px-5 py-2.5 rounded-xl text-white text-sm font-medium hover:opacity-90 inline-block" style="background:#0EA5E9">
                Jelajahi Katalog
            </a>
        </div>
        @endforelse
    </div>

    @if($borrowings->hasPages())
    <div class="mt-6">{{ $borrowings->links() }}</div>
    @endif

@endsection