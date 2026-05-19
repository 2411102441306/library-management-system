<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category');

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status === 'available') {
            $query->where('stock', '>', 0);
        } elseif ($request->status === 'empty') {
            $query->where('stock', 0);
        }

        $books      = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('admin.books.index', compact('books', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'author'         => 'required|string|max:150',
            'publisher'      => 'nullable|string|max:150',
            'isbn'           => 'nullable|string|max:20|unique:books,isbn',
            'published_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'description'    => 'nullable|string',
            'stock'          => 'required|integer|min:0',
            'category_id'    => 'required|exists:categories,id',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        Book::create($validated);

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book)
    {
        $categories = Category::all();
        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'author'         => 'required|string|max:150',
            'publisher'      => 'nullable|string|max:150',
            'isbn'           => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
            'published_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'description'    => 'nullable|string',
            'stock'          => 'required|integer|min:0',
            'category_id'    => 'required|exists:categories,id',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            // Hapus cover lama jika ada
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        $book->update($validated);

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        // Cegah hapus buku yang sedang dipinjam
        if ($book->borrowings()->whereIn('status', ['pending', 'approved', 'overdue'])->exists()) {
            return back()->with('error', 'Buku tidak dapat dihapus karena sedang dipinjam.');
        }

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}