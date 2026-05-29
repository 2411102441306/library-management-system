@extends('layouts.admin')
@section('title', 'Kelola Kategori')
@section('page-title', 'Kategori')
@section('page-subtitle', 'Kelola kategori koleksi buku perpustakaan')

@section('content')
<div class="pt-2">
    <!-- Summary -->
    <div class="grid grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#E0F2FE">
                <i class="ti ti-category text-xl" style="color:#0EA5E9"></i>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-800">{{ $categories->total() }}</div>
                <div class="text-xs text-slate-500">Total Kategori</div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#E0F2FE">
                <i class="ti ti-books text-xl" style="color:#0EA5E9"></i>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-800">{{ number_format($totalBooks) }}</div>
                <div class="text-xs text-slate-500">Total Buku Terkategori</div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#E0F2FE">
                <i class="ti ti-chart-bar text-xl" style="color:#0EA5E9"></i>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-800">
                    {{ $categories->total() > 0 ? number_format($totalBooks / $categories->total(), 0) : 0 }}
                </div>
                <div class="text-xs text-slate-500">Rata-rata Buku/Kategori</div>
            </div>
        </div>
    </div>

    <!-- Header + Search -->
    <div class="flex items-center justify-between mb-4">
        <form method="GET" class="flex items-center gap-3">
            <div class="relative">
                <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori..."
                    class="pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 w-64">
            </div>
            <button type="submit" class="px-4 py-2.5 rounded-xl border text-sm font-medium" style="border-color:#0EA5E9;color:#0EA5E9">Cari</button>
        </form>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-medium hover:opacity-90" style="background:#0EA5E9">
            <i class="ti ti-plus"></i> Tambah Kategori
        </button>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-4 gap-4">
        @php $colors = ['#E0F2FE','#DCFCE7','#FEF3C7','#EDE9FE','#FEE2E2','#DBEAFE','#FCE7F3','#F3F4F6']; @endphp
        @forelse($categories as $i => $cat)
        <div class="bg-white rounded-xl border border-slate-200 p-5 hover:border-sky-200 transition-all">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:{{ $colors[$i % count($colors)] }}">
                    <i class="ti ti-tag text-lg" style="color:#0EA5E9"></i>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="openEdit({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->description ?? '') }}')"
                        class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-sky-500 hover:border-sky-300 transition-all">
                        <i class="ti ti-pencil text-sm"></i>
                    </button>
                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-red-500 hover:border-red-300 transition-all">
                            <i class="ti ti-trash text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="text-sm font-semibold text-slate-800 mb-1">{{ $cat->name }}</div>
            <div class="text-xs text-slate-500 mb-3 line-clamp-2">{{ $cat->description ?? 'Tidak ada deskripsi' }}</div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500">Jumlah Buku</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:#E0F2FE;color:#0369A1">{{ $cat->books_count }}</span>
            </div>
        </div>
        @empty
        <div class="col-span-4 text-center py-12 text-slate-400">
            <i class="ti ti-category text-4xl block mb-2"></i>
            <div class="text-sm">Belum ada kategori</div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah -->
<div id="modal-add" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('modal-add').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-base font-semibold text-slate-800 mb-4">Tambah Kategori Baru</h3>
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Kategori <span class="text-red-400">*</span></label>
                <input type="text" name="name" placeholder="Nama kategori" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
                <p class="text-xs text-slate-400 mt-1">Slug akan otomatis dibuat dari nama</p>
            </div>
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="2" placeholder="Deskripsi singkat kategori (opsional)"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 resize-none"></textarea>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-white text-sm font-medium hover:opacity-90" style="background:#0EA5E9">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('modal-edit').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-base font-semibold text-slate-800 mb-4">Edit Kategori</h3>
        <form id="edit-form" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Kategori <span class="text-red-400">*</span></label>
                <input type="text" id="edit-name" name="name" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400">
            </div>
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                <textarea id="edit-desc" name="description" rows="2"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-sky-400 resize-none"></textarea>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-white text-sm font-medium hover:opacity-90" style="background:#0EA5E9">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600">Batal</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEdit(id, name, desc) {
    document.getElementById('edit-form').action = `/admin/categories/${id}`;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-desc').value = desc;
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>
@endpush
@endsection