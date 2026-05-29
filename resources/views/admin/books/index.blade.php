@extends('layouts.admin')
@section('title', 'Kelola Buku')
@section('page-title', 'Kelola Buku')
@section('page-subtitle', 'Manajemen koleksi buku perpustakaan')

@section('content')
<div class="pt-2">

    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
        <div></div>
        <a href="{{ route('admin.books.create') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-medium hover:opacity-90 transition-all" style="background:#0EA5E9">
            <i class="ti ti-plus text-base"></i> Tambah Buku
        </a>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 mb-5 flex items-center gap-3">
        <div class="flex-1 relative">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau pengarang..."
                class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
        </div>
        <select name="category_id" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none text-slate-600">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none text-slate-600">
            <option value="">Semua Status</option>
            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
            <option value="empty" {{ request('status') === 'empty' ? 'selected' : '' }}>Habis</option>
        </select>
        <button type="submit" class="px-4 py-2.5 rounded-xl border text-sm font-medium transition-all" style="border-color:#0EA5E9;color:#0EA5E9">
            Filter
        </button>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider w-10">
                        <input type="checkbox" class="rounded border-slate-300">
                    </th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider w-12">Cover</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Judul Buku</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengarang</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($books as $book)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-4"><input type="checkbox" class="rounded border-slate-300"></td>
                    <td class="px-5 py-4">
                        @if($book->cover_image)
                            <img src="{{ $book->cover_url }}" class="w-9 h-11 rounded object-cover">
                        @else
                            <div class="w-9 h-11 rounded flex items-center justify-center" style="background:#E0F2FE">
                                <i class="ti ti-book text-base" style="color:#0EA5E9"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm font-medium text-slate-800">{{ $book->title }}</td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $book->author }}</td>
                    <td class="px-5 py-4">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background:#E0F2FE;color:#0369A1">
                            {{ $book->category->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-sm text-slate-700 font-medium">{{ $book->stock }}</td>
                    <td class="px-5 py-4">
                        @if($book->stock > 0)
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background:#DCFCE7;color:#15803D">Tersedia</span>
                        @else
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background:#FEE2E2;color:#B91C1C">Habis</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.books.edit', $book) }}" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:text-sky-500 hover:border-sky-300 transition-all">
                                <i class="ti ti-pencil text-sm"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.books.destroy', $book) }}" onsubmit="return confirm('Hapus buku ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:text-red-500 hover:border-red-300 transition-all">
                                    <i class="ti ti-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-slate-400">
                        <i class="ti ti-books text-4xl block mb-2"></i>
                        <div class="text-sm">Belum ada buku</div>
                        <a href="{{ route('admin.books.create') }}" class="text-xs mt-2 inline-block" style="color:#0EA5E9">Tambah buku pertama</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($books->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between">
            <div class="text-sm text-slate-500">
                Menampilkan {{ $books->firstItem() }}-{{ $books->lastItem() }} dari {{ $books->total() }} buku
            </div>
            {{ $books->links() }}
        </div>
        @endif
    </div>
</div>
@endsection