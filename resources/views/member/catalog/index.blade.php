@extends('layouts.member')
@section('title', 'Katalog Buku')

@section('content')

    <section class="member-surface relative overflow-hidden px-6 py-6 sm:px-8 sm:py-8">
        <div class="absolute inset-y-0 right-0 w-1/3 bg-gradient-to-l from-sky-50/80 to-transparent"></div>
        <div class="relative grid gap-6 lg:grid-cols-[1.2fr_0.8fr] lg:items-end">
            <div>
                <span class="member-pill bg-sky-50 text-sky-700">Katalog digital</span>
                <h1 class="member-title mt-4">Temukan buku yang tepat tanpa terasa padat.</h1>
                <p class="member-lead">Pencarian dibuat lebih ringan, kategori lebih mudah dibaca, dan tiap buku tampil seperti kartu pilihan yang rapi.</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3">
                <div class="member-stat-card">
                    <div class="text-2xl font-bold text-slate-900">{{ $books->total() }}</div>
                    <div class="mt-1 text-xs text-slate-500">Buku ditemukan</div>
                </div>
                <div class="member-stat-card">
                    <div class="text-2xl font-bold text-slate-900">{{ $categories->count() }}</div>
                    <div class="mt-1 text-xs text-slate-500">Kategori aktif</div>
                </div>
                <div class="member-stat-card">
                    <div class="text-2xl font-bold text-slate-900">{{ request('search') ? '1' : 'All' }}</div>
                    <div class="mt-1 text-xs text-slate-500">Mode pencarian</div>
                </div>
            </div>
        </div>

        <form method="GET" class="relative mt-8 grid gap-3 lg:grid-cols-[1fr_auto]">
            <div class="relative">
                <i class="ti ti-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, atau kata kunci buku"
                    class="member-input pl-11 pr-4">
            </div>
            <button type="submit" class="member-primary-btn whitespace-nowrap">
                <i class="ti ti-search mr-2 text-base"></i>
                Cari Buku
            </button>

            <div class="lg:col-span-2 flex flex-wrap gap-2 pt-2">
                <a href="{{ route('member.catalog') }}" class="member-pill {{ !request('category_id') ? 'bg-sky-500 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-sky-200' }}" style="{{ !request('category_id') ? 'background:#0EA5E9' : '' }}">
                    Semua kategori
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('member.catalog', ['category_id' => $cat->id]) }}" class="member-pill {{ request('category_id') == $cat->id ? 'bg-sky-500 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-sky-200' }}" style="{{ request('category_id') == $cat->id ? 'background:#0EA5E9' : '' }}">
                    {{ $cat->name }}
                </a>
                @endforeach
            </div>
        </form>
    </section>

    <!-- Books Grid -->
    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @forelse($books as $book)
        <article class="member-surface group flex h-full flex-col overflow-hidden transition-all duration-200 hover:-translate-y-1 hover:shadow-lg">
            <div class="flex h-56 items-center justify-center overflow-hidden bg-gradient-to-br from-sky-50 via-white to-blue-50">
                @if($book->cover_image || $book->cover_url)
                    <img src="{{ $book->cover_url }}" class="h-full w-full object-cover" alt="{{ $book->title }}" onerror="this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');">
                    <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-sky-50 to-blue-50">
                        <i class="ti ti-book text-6xl text-sky-200"></i>
                    </div>
                @else
                    <i class="ti ti-book text-6xl text-sky-200"></i>
                @endif
            </div>
            <div class="flex flex-1 flex-col p-5">
                <span class="member-pill bg-sky-50 text-sky-700">
                    {{ $book->category->name ?? 'Tanpa kategori' }}
                </span>
                <div class="mt-3 flex min-h-[8.5rem] flex-col">
                    <h3 class="line-clamp-3 break-words text-base font-semibold leading-snug text-slate-900 sm:min-h-[4.5rem] sm:line-clamp-2">
                        {{ $book->title }}
                    </h3>
                    <p class="mt-1 line-clamp-2 min-h-[2.75rem] break-words text-sm leading-5 text-slate-500">
                        {{ $book->author }}
                    </p>
                </div>
                <div class="mt-4 flex items-center justify-between gap-3 text-sm">
                    <span class="inline-flex items-center rounded-full bg-slate-50 px-3 py-1 text-xs font-medium text-slate-500 ring-1 ring-slate-200">
                        Tersedia
                    </span>
                    <span class="inline-flex min-w-[96px] items-center justify-center rounded-full px-3 py-1 text-xs font-semibold {{ $book->stock > 0 ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-rose-50 text-rose-700 ring-1 ring-rose-100' }}">
                        {{ $book->stock }} eksemplar
                    </span>
                </div>
                <a href="{{ route('member.catalog.show', $book) }}"
                    class="mt-5 block w-full rounded-2xl py-3 text-center text-sm font-semibold text-white shadow-sm transition hover:shadow-md"
                    style="background:#0EA5E9">
                    Lihat Detail
                </a>
            </div>
        </article>
        @empty
        <div class="col-span-full member-empty">
            <i class="ti ti-books text-5xl text-slate-300"></i>
            <div class="mt-4 text-base font-semibold text-slate-700">Buku tidak ditemukan</div>
            <p class="mt-2 text-sm text-slate-500">Coba kata kunci lain atau pilih kategori berbeda.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($books->hasPages())
    <div class="mt-8 flex items-center justify-between gap-4 rounded-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
        <div class="text-sm text-slate-500">Menampilkan {{ $books->firstItem() }}-{{ $books->lastItem() }} dari {{ $books->total() }} buku</div>
        {{ $books->links() }}
    </div>
    @endif

@endsection