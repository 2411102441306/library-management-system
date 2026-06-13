@extends('layouts.member')
@section('title', $book->title)

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('member.catalog') }}" class="flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 mb-5">
        <i class="ti ti-arrow-left text-base"></i> Kembali ke Katalog
    </a>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="flex gap-0">
            <!-- Cover -->
            <div class="w-56 flex-shrink-0 flex items-center justify-center p-4">
                @if($book->cover_image || $book->cover_url)
                    <div class="relative w-full">
                        <img src="{{ $book->cover_url }}" class="w-full h-80 rounded-lg shadow-lg object-cover transition-all group-hover:shadow-xl" alt="{{ $book->title }}" onerror="this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');">
                        <div class="hidden w-full h-80 rounded-lg flex items-center justify-center bg-gradient-to-br from-sky-50 to-blue-50 shadow-lg">
                            <i class="ti ti-book text-7xl" style="color:#0EA5E9"></i>
                        </div>
                    </div>
                @else
                    <div class="w-full h-80 rounded-lg flex items-center justify-center bg-gradient-to-br from-sky-50 to-blue-50 shadow-lg">
                        <i class="ti ti-book text-7xl" style="color:#0EA5E9"></i>
                    </div>
                @endif
            </div>

            <!-- Detail -->
            <div class="flex-1 p-6">
                <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background:#E0F2FE;color:#0369A1">
                    {{ $book->category->name ?? '-' }}
                </span>
                <h1 class="text-xl font-bold text-slate-800 mt-3 mb-1">{{ $book->title }}</h1>
                <p class="text-slate-500 text-sm mb-4">oleh {{ $book->author }}</p>

                <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
                    @if($book->publisher)
                    <div>
                        <div class="text-xs text-slate-400 mb-0.5">Penerbit</div>
                        <div class="font-medium text-slate-700">{{ $book->publisher }}</div>
                    </div>
                    @endif
                    @if($book->published_year)
                    <div>
                        <div class="text-xs text-slate-400 mb-0.5">Tahun Terbit</div>
                        <div class="font-medium text-slate-700">{{ $book->published_year }}</div>
                    </div>
                    @endif
                    @if($book->isbn)
                    <div>
                        <div class="text-xs text-slate-400 mb-0.5">ISBN</div>
                        <div class="font-medium text-slate-700">{{ $book->isbn }}</div>
                    </div>
                    @endif
                    <div>
                        <div class="text-xs text-slate-400 mb-0.5">Stok Tersedia</div>
                        <div class="font-semibold {{ $book->stock > 0 ? '' : 'text-red-500' }}" style="{{ $book->stock > 0 ? 'color:#0EA5E9' : '' }}">
                            {{ $book->stock }} buku
                        </div>
                    </div>
                </div>

                @if($book->description)
                <div class="mb-5">
                    <div class="text-xs text-slate-400 mb-1">Deskripsi</div>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $book->description }}</p>
                </div>
                @endif

                @if($alreadyBorrowed)
                    <div class="flex items-center gap-2 p-3 rounded-xl text-sm mb-3" style="background:#FEF3C7;color:#B45309">
                        <i class="ti ti-info-circle"></i>
                        Kamu sudah meminjam buku ini dan belum mengembalikannya.
                    </div>
                @elseif(!empty($missingProfileFields))
                    <div class="p-4 rounded-xl border border-amber-200 bg-amber-50 mb-3">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center bg-amber-100 text-amber-700 flex-shrink-0">
                                <i class="ti ti-id"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-amber-900">Profil belum lengkap</div>
                                <p class="text-sm text-amber-800 mt-1">
                                    Untuk mengurangi risiko peminjaman yang salah, lengkapi dulu: {{ implode(', ', $missingProfileFields) }}.
                                </p>
                                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-amber-900 underline underline-offset-2">
                                    Lengkapi profil sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif($book->stock <= 0)
                    <div class="flex items-center gap-2 p-3 rounded-xl text-sm mb-3" style="background:#FEE2E2;color:#B91C1C">
                        <i class="ti ti-alert-circle"></i>
                        Stok buku sedang tidak tersedia.
                    </div>
                @else
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 p-4">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                                <i class="ti ti-alert-triangle"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-rose-900">Perhatian biaya keterlambatan</div>
                                <p class="mt-1 text-sm leading-6 text-rose-800">
                                    Denda dihitung per hari, jadi kalau telat 1 hari akan menjadi Rp {{ number_format($borrowPolicy['daily_fine'], 0, ',', '.') }}, 2 hari menjadi Rp {{ number_format($borrowPolicy['daily_fine'] * 2, 0, ',', '.') }}, dan bila buku hilang denda tetap sebesar Rp {{ number_format($borrowPolicy['lost_fee'], 0, ',', '.') }}.
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
                                    <span class="rounded-full bg-white px-3 py-1 text-rose-700 ring-1 ring-rose-200">1 hari telat = Rp {{ number_format($borrowPolicy['daily_fine'], 0, ',', '.') }}</span>
                                    <span class="rounded-full bg-white px-3 py-1 text-rose-700 ring-1 ring-rose-200">2 hari telat = Rp {{ number_format($borrowPolicy['daily_fine'] * 2, 0, ',', '.') }}</span>
                                    <span class="rounded-full bg-white px-3 py-1 text-rose-700 ring-1 ring-rose-200">Hilang = Rp {{ number_format($borrowPolicy['lost_fee'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('member.catalog.borrow', $book) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-xs text-slate-500 mb-1">Catatan (opsional)</label>
                            <input type="text" name="notes" placeholder="Tambahkan catatan untuk admin..."
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-500 mb-1">Durasi pinjam</label>
                            <select name="loan_days" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
                                @for($days = $borrowPolicy['min_days']; $days <= $borrowPolicy['max_days']; $days++)
                                    <option value="{{ $days }}" @selected(old('loan_days', $borrowPolicy['default_days']) == $days)>{{ $days }} hari</option>
                                @endfor
                            </select>
                        </div>
                        <div class="text-xs text-slate-400 mb-3">
                            <i class="ti ti-clock text-xs"></i> Durasi peminjaman: {{ $borrowPolicy['min_days'] }}-{{ $borrowPolicy['max_days'] }} hari. Tenggat dihitung sejak pengajuan dan admin dapat memantau sisa waktu secara langsung.
                        </div>
                        <button type="submit" class="w-full py-3 rounded-xl text-white font-medium text-sm hover:opacity-90" style="background:#0EA5E9">
                            <i class="ti ti-clipboard-plus mr-1"></i> Ajukan Peminjaman
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection