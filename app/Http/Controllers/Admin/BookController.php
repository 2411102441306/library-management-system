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
            'cover_url'      => 'nullable|url|max:255',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
            $validated['cover_url'] = null;
        } elseif ($request->filled('cover_url')) {
            $validated['cover_url'] = $request->input('cover_url');
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
            'cover_url'      => 'nullable|url|max:255',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
            $validated['cover_url'] = null;
        } elseif ($request->filled('cover_url')) {
            $validated['cover_url'] = $request->input('cover_url');
        } else {
            unset($validated['cover_url']);
        }

        $book->update($validated);

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        // Cegah hapus buku yang masih punya peminjaman aktif
        if ($book->borrowings()->whereIn('status', ['pending', 'approved', 'overdue'])->exists()) {
            return back()->with('error', 'Buku tidak dapat dihapus karena masih memiliki peminjaman aktif.');
        }

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }

    
     // Integrasi API Google Books untuk pencarian data buku otomatis.
    public function searchGoogleBooks(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return response()->json([
                'error' => 'Query pencarian tidak boleh kosong'
            ], 400);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->withHeaders([
                    'Referer' => config('app.url'),
                ])
                ->get('https://www.googleapis.com/books/v1/volumes', [
                    'q'          => $query,
                    'maxResults' => 8,
                    'printType'  => 'books',
                    'key'        => env('GOOGLE_BOOKS_API_KEY', ''),
                ]);

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Gagal mengambil data dari Google Books'
                ], 500);
            }

            $items = $response->json('items') ?? [];

            $books = collect($items)->map(function ($item) {
                $info = $item['volumeInfo'] ?? [];
                return [
                    'title'          => $info['title'] ?? '',
                    'author'         => isset($info['authors'])
                                            ? implode(', ', $info['authors'])
                                            : '',
                    'publisher'      => $info['publisher'] ?? '',
                    'published_year' => isset($info['publishedDate'])
                                            ? substr($info['publishedDate'], 0, 4)
                                            : '',
                    'isbn'           => collect($info['industryIdentifiers'] ?? [])
                                            ->firstWhere('type', 'ISBN_13')['identifier']
                                            ?? collect($info['industryIdentifiers'] ?? [])
                                            ->firstWhere('type', 'ISBN_10')['identifier']
                                            ?? '',
                    'description'    => isset($info['description'])
                                            ? substr($info['description'], 0, 500)
                                            : '',
                    'category'       => isset($info['categories'][0]) ? $info['categories'][0] : '',
                    'cover'          => $info['imageLinks']['thumbnail']
                                            ?? $info['imageLinks']['smallThumbnail']
                                            ?? '',
                ];
            })->filter(fn($book) => !empty($book['title']))->values();

            return response()->json(['books' => $books]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Koneksi ke Google Books gagal: ' . $e->getMessage()
            ], 500);
        }
    }
}