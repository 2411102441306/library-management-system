@extends('layouts.member')
@section('title', 'Katalog Buku')

@section('content')

    <!-- Hero Search -->
    <div class="text-center py-8 mb-6">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Temukan Buku Favoritmu</h1>
        <p class="text-slate-500 text-sm mb-5">Jelajahi koleksi buku perpustakaan kami</p>
        <form method="GET" class="flex items-center gap-3 max-w-xl mx-auto">
            <div class="flex-1 relative">
                <i class="ti ti-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau pengarang..."
                    class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 bg-white shadow-sm">
            </div>
            <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-medium hover:opacity-90 shadow-sm" style="background:#0EA5E9">
                Cari
            </button>
        </form>

        <!-- Category Pills -->
        <div class="flex items-center justify-center gap-2 mt-4 flex-wrap">
            <a href="{{ route('member.catalog') }}" class="px-4 py-1.5 rounded-full text-xs font-medium transition-all {{ !request('category_id') ? 'text-white' : 'bg-white border border-slate-200 text-slate-600 hover:border-sky-300' }}" style="{{ !request('category_id') ? 'background:#0EA5E9' : '' }}">
                Semua
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('member.catalog', ['category_id' => $cat->id]) }}" class="px-4 py-1.5 rounded-full text-xs font-medium transition-all {{ request('category_id') == $cat->id ? 'text-white' : 'bg-white border border-slate-200 text-slate-600 hover:border-sky-300' }}" style="{{ request('category_id') == $cat->id ? 'background:#0EA5E9' : '' }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </div>

    <!-- Books Grid -->
    <div class="grid grid-cols-4 gap-5">
        @forelse($books as $book)
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-md hover:border-sky-200 transition-all group">
            <!-- Cover -->
            <div class="h-48 flex items-center justify-center overflow-hidden" style="background:#F0F9FF">
                @if($book->cover_image)
                    <img src="{{ $book->cover_url }}" class="h-full w-full object-cover">
                @else
                    <i class="ti ti-book text-5xl" style="color:#BAE6FD"></i>
                @endif
            </div>
            <!-- Info -->
            <div class="p-4">
                <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background:#E0F2FE;color:#0369A1">
                    {{ $book->category->name ?? '-' }}
                </span>
                <h3 class="text-sm font-semibold text-slate-800 mt-2 mb-0.5 line-clamp-2 leading-tight">{{ $book->title }}</h3>
                <p class="text-xs text-slate-500 mb-3">{{ $book->author }}</p>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-slate-500">Tersedia:</span>
                    <span class="text-xs font-semibold {{ $book->stock > 0 ? '' : 'text-red-500' }}" style="{{ $book->stock > 0 ? 'color:#0EA5E9' : '' }}">
                        {{ $book->stock }} buku
                    </span>
                </div>
                <a href="{{ route('member.catalog.show', $book) }}"
                    class="block w-full text-center py-2 rounded-lg text-xs font-medium transition-all {{ $book->stock > 0 ? 'text-white hover:opacity-90' : 'bg-slate-100 text-slate-400 cursor-not-allowed' }}"
                    style="{{ $book->stock > 0 ? 'background:#0EA5E9' : '' }}">
                    {{ $book->stock > 0 ? 'Pinjam Sekarang' : 'Stok Habis' }}
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-4 text-center py-16 text-slate-400">
            <i class="ti ti-books text-5xl block mb-3"></i>
            <div class="text-base font-medium">Buku tidak ditemukan</div>
            <p class="text-sm mt-1">Coba kata kunci lain atau pilih kategori berbeda</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($books->hasPages())
    <div class="mt-8 flex items-center justify-between">
        <div class="text-sm text-slate-500">Menampilkan {{ $books->firstItem() }}-{{ $books->lastItem() }} dari {{ $books->total() }} buku</div>
        {{ $books->links() }}
    </div>
    @endif

@endsection