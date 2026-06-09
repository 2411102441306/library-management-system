@extends('layouts.admin')
@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku')
@section('page-subtitle', 'Perbarui informasi buku')

@section('content')
<div class="pt-2 max-w-3xl">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul Buku <span class="text-red-400">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $book->title) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 @error('title') border-red-400 @enderror">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pengarang <span class="text-red-400">*</span></label>
                    <input type="text" name="author" value="{{ old('author', $book->author) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Penerbit</label>
                    <input type="text" name="publisher" value="{{ old('publisher', $book->publisher) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tahun Terbit</label>
                    <input type="number" name="published_year" value="{{ old('published_year', $book->published_year) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Kategori <span class="text-red-400">*</span></label>
                    <select name="category_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Stok <span class="text-red-400">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', $book->stock) }}" min="0"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 resize-none">{{ old('description', $book->description) }}</textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Cover Buku</label>
                    @if($book->cover_image || $book->cover_url)
                        <div class="mb-3 flex items-center gap-3">
                            <div class="relative">
                                <img src="{{ $book->cover_url }}" class="w-16 h-20 rounded object-cover border border-slate-200 shadow-sm" onerror="this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');">
                                <div class="hidden w-16 h-20 rounded border border-slate-200 shadow-sm bg-sky-50 flex items-center justify-center">
                                    <i class="ti ti-alert-circle text-slate-300 text-sm"></i>
                                </div>
                            </div>
                            <div class="text-xs text-slate-500">
                                <p class="font-medium">Cover saat ini</p>
                                <p class="text-slate-400 mt-0.5">{{ $book->cover_image ? 'File lokal' : 'URL dari API' }}</p>
                            </div>
                        </div>
                    @endif
                    <input type="file" name="cover_image" accept="image/*"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600">
                    <p class="text-xs text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengganti cover.</p>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-6 pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 rounded-xl text-white text-sm font-medium hover:opacity-90" style="background:#0EA5E9">
                    <i class="ti ti-device-floppy mr-1"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.books.index') }}" class="px-6 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection