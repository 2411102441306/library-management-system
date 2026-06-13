@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang kembali, ' . auth()->user()->name)

@section('content')
<div class="pt-2">

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#E0F2FE">
                <i class="ti ti-books text-xl" style="color:#0EA5E9"></i>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($totalBooks) }}</div>
            <div class="text-sm text-slate-500 mt-0.5">Total Buku</div>
            <div class="text-xs text-green-500 mt-2 flex items-center gap-1">
                <i class="ti ti-trending-up"></i> +12 bulan ini
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#DCFCE7">
                <i class="ti ti-users text-xl" style="color:#22C55E"></i>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($totalMembers) }}</div>
            <div class="text-sm text-slate-500 mt-0.5">Anggota Aktif</div>
            <div class="text-xs text-green-500 mt-2 flex items-center gap-1">
                <i class="ti ti-trending-up"></i> +8 minggu ini
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#FEF3C7">
                <i class="ti ti-clock text-xl" style="color:#F59E0B"></i>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($activeBorrows) }}</div>
            <div class="text-sm text-slate-500 mt-0.5">Sedang Dipinjam</div>
            <div class="text-xs text-slate-400 mt-2 flex items-center gap-1">
                <i class="ti ti-minus"></i> dari {{ number_format($totalBooks) }} buku
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#FEE2E2">
                <i class="ti ti-alert-circle text-xl" style="color:#EF4444"></i>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($overdueBorrows) }}</div>
            <div class="text-sm text-slate-500 mt-0.5">Terlambat</div>
            <div class="text-xs text-red-400 mt-2 flex items-center gap-1">
                <i class="ti ti-alert-triangle"></i> butuh perhatian
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#FEF3C7">
                <i class="ti ti-id text-xl" style="color:#B45309"></i>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($incompleteProfiles) }}</div>
            <div class="text-sm text-slate-500 mt-0.5">Profil Belum Lengkap</div>
            <div class="text-xs text-amber-600 mt-2 flex items-center gap-1">
                <i class="ti ti-user-check"></i> perlu diselesaikan
            </div>
        </div>
    </div>

    <!-- Chart + Recent -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <!-- Chart -->
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-700">Aktivitas Peminjaman 7 Hari Terakhir</h3>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background:#E0F2FE;color:#0369A1">7 hari</span>
            </div>
            <canvas id="borrowChart" height="130"></canvas>
        </div>

        <!-- Recent Borrowings -->
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-700">Peminjaman Terbaru</h3>
                <a href="{{ route('admin.borrowings.index') }}" class="text-xs font-medium" style="color:#0EA5E9">Lihat semua</a>
            </div>
            @forelse($recentBorrowings as $borrow)
            <div class="flex items-center gap-3 py-2.5 border-b border-slate-100 last:border-0">
                <x-user-avatar :user="$borrow->user" size="md" class="flex-shrink-0" />
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-slate-800 truncate">{{ $borrow->book->title }}</div>
                    <div class="text-xs text-slate-500">{{ $borrow->user->name }} · {{ $borrow->due_date->format('d M Y') }}</div>
                </div>
                @php
                    $statusConfig = [
                        'approved' => ['bg' => '#DCFCE7', 'color' => '#15803D', 'label' => 'Aktif'],
                        'pending'  => ['bg' => '#FEF3C7', 'color' => '#B45309', 'label' => 'Menunggu'],
                        'overdue'  => ['bg' => '#FEE2E2', 'color' => '#B91C1C', 'label' => 'Terlambat'],
                        'returned' => ['bg' => '#DBEAFE', 'color' => '#1D4ED8', 'label' => 'Selesai'],
                        'rejected' => ['bg' => '#F3F4F6', 'color' => '#6B7280', 'label' => 'Ditolak'],
                    ];
                    $cfg = $statusConfig[$borrow->status] ?? $statusConfig['pending'];
                @endphp
                <span class="text-xs px-2.5 py-1 rounded-full font-medium flex-shrink-0" style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }}">
                    {{ $cfg['label'] }}
                </span>
            </div>
            @empty
            <div class="text-center py-6 text-slate-400 text-sm">Belum ada peminjaman</div>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-700">Perlu Ditindaklanjuti</h3>
                <a href="{{ route('admin.borrowings.index', ['status' => 'approved']) }}" class="text-xs font-medium" style="color:#0EA5E9">Lihat aktif</a>
            </div>
            @forelse($dueSoonBorrows as $borrow)
                <div class="flex items-center gap-3 py-2.5 border-b border-slate-100 last:border-0">
                    <x-user-avatar :user="$borrow->user" size="md" class="flex-shrink-0" />
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-slate-800 truncate">{{ $borrow->book->title }}</div>
                        <div class="text-xs text-slate-500">{{ $borrow->user->name }} · {{ $borrow->due_date->format('d M Y') }}</div>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium flex-shrink-0" style="background:#FEF3C7;color:#B45309">
                        {{ $borrow->days_remaining }} hari lagi
                    </span>
                </div>
            @empty
                <div class="text-center py-6 text-slate-400 text-sm">Tidak ada pinjaman jatuh tempo dalam 2 hari ke depan</div>
            @endforelse
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-700">Pinjaman Terlambat</h3>
                <a href="{{ route('admin.borrowings.index', ['status' => 'overdue']) }}" class="text-xs font-medium" style="color:#0EA5E9">Lihat terlambat</a>
            </div>
            @forelse($overdueBorrowings as $borrow)
            <div class="flex items-center gap-3 py-2.5 border-b border-slate-100 last:border-0">
                <x-user-avatar :user="$borrow->user" size="md" class="flex-shrink-0" />
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-slate-800 truncate">{{ $borrow->book->title }}</div>
                    <div class="text-xs text-slate-500">{{ $borrow->user->name }} · {{ $borrow->due_date->format('d M Y') }}</div>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium flex-shrink-0" style="background:#FEE2E2;color:#B91C1C">
                    {{ $borrow->days_late }} hari terlambat
                </span>
            </div>
            @empty
            <div class="text-center py-6 text-slate-400 text-sm">Belum ada pinjaman terlambat</div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-3 gap-3">
            <a href="{{ route('admin.books.create') }}" class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50 transition-all group">
                <i class="ti ti-book-upload text-xl" style="color:#0EA5E9"></i>
                <span class="text-sm font-medium text-slate-700">Tambah Buku</span>
            </a>
            <a href="{{ route('admin.members.create') }}" class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50 transition-all group">
                <i class="ti ti-user-plus text-xl" style="color:#0EA5E9"></i>
                <span class="text-sm font-medium text-slate-700">Tambah Anggota</span>
            </a>
            <a href="{{ route('admin.borrowings.index') }}" class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50 transition-all group">
                <i class="ti ti-clipboard-list text-xl" style="color:#0EA5E9"></i>
                <span class="text-sm font-medium text-slate-700">Kelola Peminjaman</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dashboardRefreshInterval = 30000;

const ctx = document.getElementById('borrowChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            data: @json($chartData),
            backgroundColor: @json($chartData).map((_, i) =>
                i === @json($chartData).indexOf(Math.max(...@json($chartData)))
                    ? '#0EA5E9' : '#BAE6FD'
            ),
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#F1F5F9' }, ticks: { color: '#94A3B8', font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { size: 11 } } }
        }
    }
});

setTimeout(() => {
    window.setInterval(() => window.location.reload(), dashboardRefreshInterval);
}, 0);
</script>
@endpush