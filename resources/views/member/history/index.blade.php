@extends('layouts.member')
@section('title', 'Riwayat Peminjaman')

@section('content')

    <section class="member-surface px-6 py-6 sm:px-8 sm:py-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="member-pill bg-sky-50 text-sky-700">Riwayat peminjaman</span>
                <h1 class="member-title mt-4">Semua status pinjaman dalam satu alur yang mudah dibaca.</h1>
                <p class="member-lead">Tampilan ini dirancang supaya member cepat menangkap status, tenggat, dan langkah berikutnya tanpa harus mencari-cari.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-3 lg:w-[520px]">
                <div class="member-stat-card">
                    <div class="flex items-center gap-3">
                        <div class="member-stat-icon bg-sky-50 text-sky-600"><i class="ti ti-clipboard-list"></i></div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900">{{ $counts['all'] }}</div>
                            <div class="text-xs text-slate-500">Total pinjaman</div>
                        </div>
                    </div>
                </div>
                <div class="member-stat-card">
                    <div class="flex items-center gap-3">
                        <div class="member-stat-icon bg-emerald-50 text-emerald-600"><i class="ti ti-clock"></i></div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900">{{ $counts['approved'] }}</div>
                            <div class="text-xs text-slate-500">Sedang aktif</div>
                        </div>
                    </div>
                </div>
                <div class="member-stat-card">
                    <div class="flex items-center gap-3">
                        <div class="member-stat-icon bg-rose-50 text-rose-600"><i class="ti ti-alert-circle"></i></div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900">{{ $counts['overdue'] }}</div>
                            <div class="text-xs text-slate-500">Perlu perhatian</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 grid gap-3 lg:grid-cols-3">
            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-800">
                <div class="font-semibold text-sky-900">1. Status pinjaman</div>
                <p class="mt-1 leading-6">Aktif, terlambat, hilang, atau selesai ditampilkan jelas di setiap kartu.</p>
            </div>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <div class="font-semibold text-rose-900">2. Denda belum lunas</div>
                <p class="mt-1 leading-6">Kalau masih ada denda, kartu akan menampilkan peringatan supaya Anda tahu masih perlu tindak lanjut.</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                <div class="font-semibold text-emerald-900">3. Denda lunas</div>
                <p class="mt-1 leading-6">Setelah admin menandai lunas, peringatan hilang dan yang tampil hanya histori penyelesaiannya.</p>
            </div>
        </div>

        <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
            <div class="flex items-start gap-3">
                <i class="ti ti-shield-alert mt-0.5 text-lg text-rose-600"></i>
                <div>
                    <div class="font-semibold text-rose-900">Ringkasan risiko denda</div>
                    <p class="mt-1 leading-6">
                        Denda keterlambatan bertambah setiap hari. Jika terlambat 1 hari, denda muncul sesuai tarif harian. Jika 2 hari, totalnya menjadi dua kali tarif harian. Jika buku hilang, denda tetap memakai nominal kehilangan yang sudah ditetapkan admin.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tabs -->
    <div class="mt-6 flex flex-wrap items-center gap-2 rounded-3xl border border-slate-200 bg-white p-2 shadow-sm">
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
        <a href="{{ route('member.history', ['status' => $key]) }}"
            class="member-tab {{ request('status', 'all') === $key ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}"
            style="{{ request('status', 'all') === $key ? 'background:#0EA5E9' : '' }}">
            {{ $tab['label'] }}
            @if($tab['count'] > 0)
            <span class="rounded-full px-2 py-0.5 text-xs" style="{{ request('status', 'all') === $key ? 'background:rgba(255,255,255,0.18);color:white' : 'background:#F1F5F9;color:#64748B' }}">
                {{ $tab['count'] }}
            </span>
            @endif
        </a>
        @endforeach
    </div>

    <!-- Borrowing Cards -->
    <div class="mt-6 space-y-4">
        @forelse($borrowings as $borrow)
        @php
            $isOverdue = $borrow->status === 'overdue';
            $cfg = match($borrow->status) {
                'approved' => ['#DCFCE7','#15803D','Aktif'],
                'pending'  => ['#FEF3C7','#B45309','Menunggu Persetujuan'],
                'overdue'  => ['#FEE2E2','#B91C1C','Terlambat'],
                'lost'     => ['#FEE2E2','#B91C1C','Hilang'],
                'returned' => ['#DBEAFE','#1D4ED8','Selesai'],
                default    => ['#F3F4F6','#6B7280','Ditolak'],
            };
        @endphp
        <article class="member-surface overflow-hidden {{ ($isOverdue || $borrow->status === 'lost') && $borrow->hasOutstandingFine() ? 'ring-1 ring-rose-200' : '' }}">
            <div class="grid gap-4 p-5 lg:grid-cols-[96px_1fr] lg:items-center">
                <div class="flex h-28 w-24 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-sky-50 via-white to-blue-50 shadow-sm">
                    @if($borrow->book->cover_image || $borrow->book->cover_url)
                        <img src="{{ $borrow->book->cover_url }}" class="w-full h-full object-cover" alt="{{ $borrow->book->title }}" onerror="this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');">
                        <div class="hidden w-full h-full items-center justify-center bg-gradient-to-br from-sky-50 to-blue-50">
                            <i class="ti ti-book text-xl text-sky-200"></i>
                        </div>
                    @else
                        <i class="ti ti-book text-xl text-sky-200"></i>
                    @endif
                </div>

                <div class="min-w-0">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ $borrow->book->category->name ?? 'Buku' }}</div>
                            <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ $borrow->book->title }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $borrow->book->author }}</p>
                        </div>
                        <span class="member-pill flex-shrink-0" style="background:{{ $cfg[0] }};color:{{ $cfg[1] }}">
                            {{ $cfg[2] }}
                        </span>
                    </div>
                    <div class="mt-4 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-3 py-1">
                            <i class="ti ti-calendar text-xs"></i>
                            Diajukan: {{ $borrow->created_at->format('d M Y H:i') }}
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-3 py-1 {{ $isOverdue || $borrow->status === 'lost' ? 'font-semibold' : '' }}" style="{{ ($isOverdue || $borrow->status === 'lost') ? 'color:#EF4444' : '' }}">
                            <i class="ti ti-clock text-xs"></i>
                            Jatuh tempo: {{ $borrow->due_date->format('d M Y') }}
                            @if($isOverdue)
                                ({{ $borrow->days_late }} hari terlambat)
                            @endif
                        </span>
                        @if($borrow->due_soon_warning)
                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-3 py-1 font-semibold text-rose-700">
                            <i class="ti ti-alert-triangle text-xs"></i>
                            Hampir jatuh tempo
                        </span>
                        @endif
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-3 py-1">
                            <i class="ti ti-hourglass text-xs"></i>
                            Durasi: {{ $borrow->loan_period_label }}
                        </span>
                        @if($borrow->hasOutstandingFine())
                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-3 py-1 font-semibold text-rose-700">
                            <i class="ti ti-report-money text-xs"></i>
                            @if($borrow->status === 'lost')
                                Denda hilang belum lunas: Rp {{ number_format($borrow->fine_amount, 0, ',', '.') }}
                            @else
                                Denda terlambat belum lunas: Rp {{ number_format($borrow->fine_amount, 0, ',', '.') }}
                            @endif
                        </span>
                        @elseif($borrow->fine_amount > 0)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 font-semibold text-emerald-700">
                            <i class="ti ti-badge-check text-xs"></i>
                            Denda lunas: Rp {{ number_format($borrow->fine_amount, 0, ',', '.') }}
                        </span>
                        @endif
                        @if($borrow->hasOutstandingFine() && !$borrow->hasFineProof())
                        <div class="w-full rounded-2xl border border-dashed border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            <div class="flex items-start gap-3">
                                <i class="ti ti-upload text-lg text-amber-600"></i>
                                <div class="flex-1">
                                    <div class="font-semibold text-amber-900">Kirim bukti pembayaran denda</div>
                                    <p class="mt-1 leading-6">Unggah bukti transfer atau foto pembayaran agar admin bisa memverifikasi dan menutup peringatan denda.</p>
                                    <form method="POST" action="{{ route('member.history.fine-proof', $borrow) }}" enctype="multipart/form-data" class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center">
                                        @csrf
                                        <input type="file" name="fine_proof" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-full file:border-0 file:bg-amber-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-amber-800 hover:file:bg-amber-200 sm:max-w-sm">
                                        <button type="submit" class="member-primary-btn inline-flex justify-center sm:w-auto">Kirim Bukti</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @elseif($borrow->hasOutstandingFine() && $borrow->hasFineProof())
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 font-semibold text-amber-700">
                            <i class="ti ti-receipt text-xs"></i>
                            Bukti pembayaran terkirim, menunggu verifikasi admin
                        </span>
                        @endif
                        @if($borrow->return_date)
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-3 py-1">
                            <i class="ti ti-check text-xs"></i>
                            Dikembalikan: {{ $borrow->return_date->format('d M Y') }}
                        </span>
                        @endif
                        @if($borrow->status === 'lost' && $borrow->hasOutstandingFine())
                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-3 py-1 font-semibold text-rose-700">
                            <i class="ti ti-badge-off text-xs"></i>
                            Buku ditandai hilang
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </article>
        @empty
        <div class="member-empty">
            <i class="ti ti-clipboard-list text-5xl text-slate-300"></i>
            <div class="mt-4 text-base font-semibold text-slate-700">Belum ada riwayat peminjaman</div>
            <p class="mt-2 text-sm text-slate-500">Mulai jelajahi koleksi buku kami.</p>
            <a href="{{ route('member.catalog') }}" class="member-primary-btn mt-5 inline-flex">
                Jelajahi Katalog
            </a>
        </div>
        @endforelse
    </div>

    @if($borrowings->hasPages())
    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">{{ $borrowings->links() }}</div>
    @endif

@endsection