@extends('layouts.admin')
@section('title', 'Tambah Buku')
@section('page-title', 'Tambah Buku')
@section('page-subtitle', 'Tambah koleksi buku baru ke perpustakaan')

@section('content')
{{-- Menggunakan grid 3 kolom pada layar besar: 1 kolom untuk pencarian, 2 kolom untuk form --}}
<div class="pt-2 grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm lg:col-span-1">
        <h3 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
            <i class="ti ti-search text-base" style="color:#0EA5E9"></i>
            Cari dari Google Books (Opsional)
        </h3>
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="text" id="google-search" placeholder="Ketik judul atau ISBN..."
                class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 w-full">
            <button type="button" onclick="searchGoogleBooks()" class="px-4 py-2.5 rounded-xl text-white text-sm font-medium hover:opacity-90 flex items-center justify-center gap-1 flex-shrink-0" style="background:#0EA5E9">
                <i class="ti ti-search"></i> Cari
            </button>
        </div>
        
        <div id="search-results" class="mt-4 hidden">
            <div class="text-xs text-slate-500 mb-2 pb-2 border-b border-slate-100">Pilih buku untuk mengisi form:</div>
            {{-- Mengubah list menjadi 1 kolom saja agar rapi di panel kiri --}}
            <div id="results-list" class="grid grid-cols-1 gap-2 max-h-[400px] overflow-y-auto pr-1"></div>
        </div>
        
        <div id="search-loading" class="hidden text-center py-6">
            <i class="ti ti-loader-2 animate-spin text-xl" style="color:#0EA5E9"></i>
            <div class="text-xs text-slate-500 mt-1">Mencari buku...</div>
        </div>
        <div id="search-error" class="hidden mt-2 text-xs text-red-500"></div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm lg:col-span-2">
        <h3 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-50">Detail Buku</h3>
        <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul Buku <span class="text-red-400">*</span></label>
                    <input type="text" name="title" id="field-title" value="{{ old('title') }}" placeholder="Judul buku"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 @error('title') border-red-400 @enderror">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pengarang <span class="text-red-400">*</span></label>
                    <input type="text" name="author" id="field-author" value="{{ old('author') }}" placeholder="Nama pengarang"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 @error('author') border-red-400 @enderror">
                    @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Penerbit</label>
                    <input type="text" name="publisher" id="field-publisher" value="{{ old('publisher') }}" placeholder="Nama penerbit"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">ISBN</label>
                    <input type="text" name="isbn" id="field-isbn" value="{{ old('isbn') }}" placeholder="978-xxx-xxx"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 @error('isbn') border-red-400 @enderror">
                    @error('isbn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tahun Terbit</label>
                    <input type="number" name="published_year" id="field-year" value="{{ old('published_year') }}" placeholder="{{ date('Y') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Kategori <span class="text-red-400">*</span></label>
                    <select name="category_id" id="field-category" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 @error('category_id') border-red-400 @enderror">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Stok <span class="text-red-400">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', 1) }}" min="0"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 @error('stock') border-red-400 @enderror">
                    @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="description" id="field-description" rows="4" placeholder="Deskripsi singkat buku..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Cover Buku</label>
                    <input type="hidden" name="cover_url" id="field-cover-url" value="{{ old('cover_url') }}">
                    <div id="cover-preview" class="hidden mb-3 flex items-center gap-3">
                        <div class="relative">
                            <img id="cover-img" src="" class="w-24 h-36 rounded-lg object-cover border border-slate-200 shadow-sm" onerror="this.classList.add('hidden'); document.getElementById('cover-img-error')?.classList.remove('hidden');">
                            <div id="cover-img-error" class="hidden w-24 h-36 rounded-lg border border-slate-200 shadow-sm bg-sky-50 flex items-center justify-center">
                                <i class="ti ti-alert-circle text-slate-400 text-lg"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-sky-600 font-medium">✓ Berhasil memuat cover</p>
                            <p class="text-xs text-slate-400 mt-1">dari Google Books API</p>
                        </div>
                    </div>
                    <input type="file" name="cover_image" accept="image/*"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                    <p class="text-xs text-slate-400 mt-1">Upload cover manual, atau gunakan pencarian Google Books di sebelah kiri untuk cover otomatis.</p>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 rounded-xl text-white text-sm font-medium hover:opacity-90 flex items-center gap-2 transition-all" style="background:#0EA5E9">
                    <i class="ti ti-device-floppy"></i> Simpan Buku
                </button>
                <a href="{{ route('admin.books.index') }}" class="px-6 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function searchGoogleBooks() {
    const query = document.getElementById('google-search').value.trim();
    if (!query) return;

    document.getElementById('search-loading').classList.remove('hidden');
    document.getElementById('search-results').classList.add('hidden');
    document.getElementById('search-error').classList.add('hidden');

    try {
        const res = await fetch(`/admin/books/search-api?q=${encodeURIComponent(query)}`, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await res.json();
        document.getElementById('search-loading').classList.add('hidden');

        if (data.error || !data.books?.length) {
            document.getElementById('search-error').textContent = data.error || 'Buku tidak ditemukan.';
            document.getElementById('search-error').classList.remove('hidden');
            return;
        }

        const list = document.getElementById('results-list');
        list.innerHTML = '';
        data.books.forEach(book => {
            list.innerHTML += `
                <div onclick='fillForm(${JSON.stringify(book)})' class="flex items-start gap-2.5 p-2.5 rounded-lg border border-slate-200 hover:border-sky-300 hover:bg-sky-50/60 cursor-pointer transition-all">
                    ${book.cover ? `<img src="${book.cover}" class="w-9 h-13 rounded object-cover flex-shrink-0 border border-slate-100 shadow-sm">` : `<div class="w-9 h-13 rounded flex items-center justify-center bg-sky-50 flex-shrink-0 border border-slate-100"><i class="ti ti-book text-sky-400 text-base"></i></div>`}
                    <div class="min-w-0">
                        <div class="text-xs font-semibold text-slate-800 leading-tight line-clamp-2">${book.title}</div>
                        <div class="text-[11px] text-slate-500 mt-0.5 truncate">${book.author || '-'}</div>
                        <div class="text-[10px] text-slate-400">${book.published_year || ''}${book.category ? ' · ' + book.category : ''}</div>
                    </div>
                </div>`;
        });
        document.getElementById('search-results').classList.remove('hidden');
    } catch (e) {
        document.getElementById('search-loading').classList.add('hidden');
        document.getElementById('search-error').textContent = 'Gagal menghubungi server.';
        document.getElementById('search-error').classList.remove('hidden');
    }
}

function fillForm(book) {
    document.getElementById('field-title').value       = book.title || '';
    document.getElementById('field-author').value      = book.author || '';
    document.getElementById('field-publisher').value   = book.publisher || '';
    document.getElementById('field-isbn').value        = book.isbn || '';
    document.getElementById('field-year').value        = book.published_year || '';
    document.getElementById('field-description').value = book.description || '';

    if (book.category) {
        const categorySelect = document.getElementById('field-category');
        const match = Array.from(categorySelect.options).find(option =>
            option.textContent.trim().toLowerCase() === book.category.toLowerCase()
        );
        if (match) {
            categorySelect.value = match.value;
        }
    }

    if (book.cover) {
        document.getElementById('field-cover-url').value = book.cover;
        document.getElementById('cover-img').src = book.cover;
        document.getElementById('cover-preview').classList.remove('hidden');
    }
    document.getElementById('search-results').classList.add('hidden');
}

document.getElementById('google-search').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); searchGoogleBooks(); }
});

window.addEventListener('DOMContentLoaded', () => {
    const coverUrl = document.getElementById('field-cover-url').value;
    if (coverUrl) {
        document.getElementById('cover-img').src = coverUrl;
        document.getElementById('cover-preview').classList.remove('hidden');
    }
});
</script>
@endpush