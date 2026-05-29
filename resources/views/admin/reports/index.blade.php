@extends('layouts.admin')
@section('title', 'Laporan')
@section('page-title', 'Laporan')
@section('page-subtitle', 'Statistik dan analitik aktivitas perpustakaan')

@section('content')
<div class="pt-2">

    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1 shadow-sm">
            @foreach(['7days' => '7 Hari Terakhir', '30days' => '30 Hari Terakhir', '6months' => '6 Bulan Terakhir', '1year' => '1 Tahun Terakhir'] as $key => $label)
            <a href="{{ route('admin.reports.index', ['period' => $key]) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
                style="{{ $period === $key ? 'background:#0EA5E9;color:white' : 'color:#64748B' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#E0F2FE">
                <i class="ti ti-clipboard-list text-xl" style="color:#0EA5E9"></i>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($totalBorrowings) }}</div>
            <div class="text-sm text-slate-500 mt-0.5">Total Peminjaman</div>
            <div class="text-xs text-green-500 mt-2 flex items-center gap-1">
                <i class="ti ti-trending-up"></i> Periode ini
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#DCFCE7">
                <i class="ti ti-users text-xl" style="color:#22C55E"></i>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($totalMembers) }}</div>
            <div class="text-sm text-slate-500 mt-0.5">Anggota Aktif</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#FEF3C7">
                <i class="ti ti-clock-check text-xl" style="color:#F59E0B"></i>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ $onTimePercentage }}%</div>
            <div class="text-sm text-slate-500 mt-0.5">Dikembalikan Tepat Waktu</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#FEE2E2">
                <i class="ti ti-alert-circle text-xl" style="color:#EF4444"></i>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($overdueBorrowings) }}</div>
            <div class="text-sm text-slate-500 mt-0.5">Peminjaman Terlambat</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
        
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Peminjaman per Bulan</h3>
            {{-- Wrapper pembatas tinggi chart --}}
            <div class="relative h-64 w-full">
                <canvas id="monthChart"></canvas>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Distribusi Kategori</h3>
                {{-- Wrapper penyeimbang posisi agar doughnut berada di tengah dan tidak over-scale --}}
                <div class="relative h-44 w-full flex justify-center items-center mb-4">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
            
            <div class="space-y-1.5 border-t border-slate-50 pt-3">
                @foreach($categoryStats->take(5) as $i => $cat)
                @php $colors = ['#0EA5E9','#8B5CF6','#22C55E','#F59E0B','#EF4444']; @endphp
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background:{{ $colors[$i % 5] }}"></div>
                        <span class="text-slate-600">{{ $cat->name }}</span>
                    </div>
                    <span class="font-medium text-slate-700">{{ $cat->borrow_count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">10 Buku Terpopuler</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase w-12">#</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Judul Buku</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Pengarang</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Kategori</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase w-32">Total Dipinjam</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($topBooks as $i => $book)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-3.5 text-sm font-medium text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-9 rounded flex items-center justify-center flex-shrink-0" style="background:#E0F2FE">
                                    <i class="ti ti-book text-xs" style="color:#0EA5E9"></i>
                                </div>
                                <span class="text-sm font-medium text-slate-800 line-clamp-1">{{ $book->title }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-600 truncate max-w-[150px]">{{ $book->author }}</td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background:#E0F2FE;color:#0369A1">
                                {{ $book->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-sm font-semibold" style="color:#0EA5E9">{{ $book->total_borrowed }}x</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-slate-400 text-sm">Belum ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Bar Chart Config
new Chart(document.getElementById('monthChart'), {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            data: @json($chartData),
            backgroundColor: '#BAE6FD',
            hoverBackgroundColor: '#0EA5E9',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false, // Wajib bernilai false agar patuh pada wrapper CSS
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#F1F5F9' }, ticks: { color: '#94A3B8', font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { size: 11 } } }
        }
    }
});

// Doughnut Chart Config
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: @json($categoryStats->pluck('name')),
        datasets: [{
            data: @json($categoryStats->pluck('borrow_count')),
            backgroundColor: ['#0EA5E9','#8B5CF6','#22C55E','#F59E0B','#EF4444','#EC4899','#14B8A6','#F97316'],
            borderWidth: 0,
            hoverOffset: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false, // Wajib bernilai false agar patuh pada wrapper CSS
        cutout: '70%',
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush