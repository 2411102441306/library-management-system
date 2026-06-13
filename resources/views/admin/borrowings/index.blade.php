@extends('layouts.admin')
@section('title', 'Kelola Peminjaman')
@section('page-title', 'Kelola Peminjaman')
@section('page-subtitle', 'Manajemen peminjaman dan pengembalian buku')

@section('content')
<div class="pt-2">
    <div class="grid gap-3 mb-5 lg:grid-cols-3">
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-rose-600">Denda menunggu lunas</div>
            <div class="mt-2 text-2xl font-bold text-rose-900">{{ $counts['fine_pending'] }}</div>
            <div class="mt-1 text-sm text-rose-700">Bisa diselesaikan dengan tombol tandai denda lunas.</div>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Denda sudah lunas</div>
            <div class="mt-2 text-2xl font-bold text-emerald-900">{{ $counts['fine_settled'] }}</div>
            <div class="mt-1 text-sm text-emerald-700">Status ini tetap menyimpan histori, tapi peringatan tidak lagi muncul.</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Alur singkat</div>
            <div class="mt-2 text-sm leading-6 text-slate-600">
                1. Buku dikembalikan atau dinyatakan hilang.
                2. Denda dihitung otomatis.
                3. Admin menandai denda lunas setelah pembayaran selesai.
            </div>
        </div>
    </div>

    <div class="flex items-center gap-1 mb-5 bg-white border border-slate-200 rounded-xl p-1 w-fit shadow-sm overflow-x-auto max-w-full">
        @php
            $tabs = [
                'all'      => ['label' => 'Semua',    'count' => $counts['all']],
                'pending'  => ['label' => 'Menunggu', 'count' => $counts['pending']],
                'approved' => ['label' => 'Aktif',    'count' => $counts['approved']],
                'overdue'  => ['label' => 'Terlambat','count' => $counts['overdue']],
                'lost'     => ['label' => 'Hilang',   'count' => $counts['lost']],
                'returned' => ['label' => 'Selesai',  'count' => $counts['returned']],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
        <a href="{{ route('admin.borrowings.index', ['status' => $key]) }}"
            class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap"
            style="{{ request('status', 'all') === $key ? 'background:#0EA5E9;color:white' : 'color:#64748B' }}">
            {{ $tab['label'] }}
            <span class="text-xs px-1.5 py-0.5 rounded-full" style="{{ request('status', 'all') === $key ? 'background:rgba(255,255,255,0.2);color:white' : 'background:#F1F5F9;color:#64748B' }}">
                {{ $tab['count'] }}
            </span>
        </a>
        @endforeach
    </div>

    <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 mb-5 flex items-center gap-3 shadow-sm">
        <input type="hidden" name="status" value="{{ request('status', 'all') }}">
        <div class="flex-1 relative">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari anggota atau buku..."
                class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 transition-all">
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl border text-sm font-medium transition-all hover:bg-sky-50/50" style="border-color:#0EA5E9;color:#0EA5E9">
            Cari
        </button>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px]">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Anggota</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Buku</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase w-36">Diajukan</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase w-24">Durasi</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase w-32">Jatuh Tempo</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase w-28">Sisa / Telat</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase w-32">Denda</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase w-28">Status</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase w-28">Peringatan</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase w-52">Catatan</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($borrowings as $borrow)
                    <tr class="hover:bg-slate-50/60 transition-colors align-middle">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2.5">
                                <x-user-avatar :user="$borrow->user" size="sm" class="flex-shrink-0" />
                                <span class="text-sm font-medium text-slate-700">{{ $borrow->user->name }}</span>
                            </div>
                        </td>
                        
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-6 h-8 rounded flex items-center justify-center flex-shrink-0" style="background:#E0F2FE">
                                    <i class="ti ti-book text-xs" style="color:#0EA5E9"></i>
                                </div>
                                <span class="text-sm text-slate-700 max-w-xs truncate font-medium">{{ $borrow->book->title }}</span>
                            </div>
                        </td>
                        
                        <td class="px-5 py-4 text-sm text-slate-600 whitespace-nowrap">{{ $borrow->created_at->format('d M Y H:i') }}</td>

                        <td class="px-5 py-4 text-sm text-slate-600 whitespace-nowrap">{{ $borrow->loan_period_label }}</td>
                        
                        <td class="px-5 py-4 text-sm whitespace-nowrap {{ $borrow->isOverdue() ? 'font-semibold' : 'text-slate-600' }}" style="{{ $borrow->isOverdue() ? 'color:#EF4444' : '' }}">
                            {{ $borrow->due_date->format('d M Y') }}
                        </td>

                        <td class="px-5 py-4 text-sm whitespace-nowrap {{ ($borrow->isOverdue() || $borrow->status === 'lost') ? 'font-semibold' : 'text-slate-600' }}" style="{{ ($borrow->isOverdue() || $borrow->status === 'lost') ? 'color:#EF4444' : '' }}">
                            @if($borrow->isOverdue())
                                {{ $borrow->days_late }} hari terlambat
                            @elseif($borrow->status === 'lost')
                                Hilang
                            @else
                                {{ $borrow->days_remaining }} hari lagi
                            @endif
                        </td>

                        <td class="px-5 py-4 text-sm whitespace-nowrap {{ $borrow->outstanding_fine_amount > 0 ? 'font-semibold' : 'text-slate-500' }}" style="{{ $borrow->outstanding_fine_amount > 0 ? 'color:#B91C1C' : '' }}">
                            @if($borrow->outstanding_fine_amount > 0)
                                {{ $borrow->status === 'lost' ? 'Denda hilang' : 'Denda terlambat' }}: Rp {{ number_format($borrow->outstanding_fine_amount, 0, ',', '.') }}
                            @elseif($borrow->fine_amount > 0)
                                Lunas: Rp {{ number_format($borrow->fine_amount, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        
                        <td class="px-5 py-4">
                            @php
                                $cfg = match($borrow->status) {
                                    'approved' => ['#DCFCE7','#15803D','Aktif'],
                                    'pending'  => ['#FEF3C7','#B45309','Menunggu'],
                                    'overdue'  => ['#FEE2E2','#B91C1C','Terlambat'],
                                    'lost'     => ['#FEE2E2','#B91C1C','Hilang'],
                                    'returned' => ['#DBEAFE','#1D4ED8','Selesai'],
                                    default    => ['#F3F4F6','#6B7280','Ditolak'],
                                };
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium inline-block whitespace-nowrap" style="background:{{ $cfg[0] }};color:{{ $cfg[1] }}">{{ $cfg[2] }}</span>
                        </td>

                        <td class="px-5 py-4 text-sm whitespace-nowrap">
                            @if($borrow->hasOutstandingFine() && $borrow->hasFineProof())
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                    <i class="ti ti-receipt text-xs"></i>
                                    Bukti terkirim
                                </span>
                            @elseif($borrow->hasOutstandingFine())
                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                    <i class="ti ti-receipt-2 text-xs"></i>
                                    Denda belum lunas
                                </span>
                            @elseif($borrow->fine_amount > 0)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    <i class="ti ti-badge-check text-xs"></i>
                                    Denda lunas
                                </span>
                            @elseif($borrow->due_soon_warning)
                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                    <i class="ti ti-alert-triangle text-xs"></i>
                                    Hampir jatuh tempo
                                </span>
                            @elseif($borrow->status === 'lost')
                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                    <i class="ti ti-badge-off text-xs"></i>
                                    Buku hilang
                                </span>
                            @elseif($borrow->isOverdue())
                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                    <i class="ti ti-alert-triangle text-xs"></i>
                                    Telat
                                </span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        
                        <td class="px-5 py-4 text-sm text-slate-500 max-w-[200px]">
                            @if($borrow->notes)
                                <div onclick="toggleNote(this)" 
                                     class="truncate cursor-pointer hover:text-sky-600 transition-all select-none" 
                                     title="Klik untuk melihat teks lengkap">
                                    {{ $borrow->notes }}
                                </div>
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                @if($borrow->status === 'pending')
                                    <form method="POST" action="{{ route('admin.borrowings.approve', $borrow) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 shadow-sm transition-all whitespace-nowrap" style="background:#22C55E">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.borrowings.reject', $borrow) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 shadow-sm transition-all whitespace-nowrap" style="background:#EF4444">Tolak</button>
                                    </form>
                                @elseif(in_array($borrow->status, ['approved', 'overdue']))
                                    <form method="POST" action="{{ route('admin.borrowings.return', $borrow) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all hover:bg-sky-50 shadow-sm whitespace-nowrap" style="border-color:#0EA5E9;color:#0EA5E9">Tandai Kembali</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.borrowings.lost', $borrow) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all hover:bg-rose-50 shadow-sm whitespace-nowrap" style="border-color:#EF4444;color:#EF4444">Tandai Hilang</button>
                                    </form>
                                @elseif(in_array($borrow->status, ['returned', 'lost']) && $borrow->hasOutstandingFine())
                                    @if($borrow->hasFineProof())
                                        <a href="{{ route('admin.borrowings.fine-proof', $borrow) }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all hover:bg-amber-50 shadow-sm whitespace-nowrap" style="border-color:#B45309;color:#B45309">Lihat Bukti</a>
                                        <form method="POST" action="{{ route('admin.borrowings.settle-fine', $borrow) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all hover:bg-emerald-50 shadow-sm whitespace-nowrap" style="border-color:#15803D;color:#15803D">Verifikasi & Lunas</button>
                                        </form>
                                    @else
                                        <span class="text-xs font-medium text-amber-700 bg-amber-50 px-2.5 py-1 rounded whitespace-nowrap">Menunggu bukti</span>
                                    @endif
                                @else
                                    <span class="text-xs font-medium text-slate-400 bg-slate-100 px-2.5 py-1 rounded whitespace-nowrap">{{ $borrow->fine_amount > 0 ? 'Denda lunas' : 'Selesai' }}</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-12 text-slate-400">
                            <i class="ti ti-clipboard-list text-4xl block mb-2 text-slate-300"></i>
                            <div class="text-sm font-medium">Tidak ada data peminjaman</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($borrowings->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="text-sm text-slate-500">Menampilkan {{ $borrowings->firstItem() }}-{{ $borrowings->lastItem() }} dari {{ $borrowings->total() }}</div>
            {{ $borrowings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Fungsi JavaScript handling ekspansi teks catatan secara dinamis
function toggleNote(element) {
    if (element.classList.contains('truncate')) {
        element.classList.remove('truncate');
        element.classList.add('whitespace-normal', 'break-words', 'bg-slate-50', 'p-2', 'rounded-lg', 'border', 'border-slate-100');
        element.setAttribute('title', 'Klik untuk menyembunyikan teks');
    } else {
        element.classList.remove('whitespace-normal', 'break-words', 'bg-slate-50', 'p-2', 'rounded-lg', 'border', 'border-slate-100');
        element.classList.add('truncate');
        element.setAttribute('title', 'Klik untuk melihat teks lengkap');
    }
}
</script>
@endpush