<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class CatalogController extends Controller
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

        if ($request->availability === 'available') {
            $query->where('stock', '>', 0);
        }

        $books      = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('member.catalog.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        // Cek apakah member sudah meminjam buku ini dan belum dikembalikan
        $alreadyBorrowed = Borrowing::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved', 'overdue'])
            ->exists();

        return view('member.catalog.show', compact('book', 'alreadyBorrowed'));
    }

    public function borrow(Request $request, Book $book)
    {
        // Validasi stok
        if ($book->stock <= 0) {
            return back()->with('error', 'Stok buku tidak tersedia.');
        }

        // Cek apakah sudah meminjam
        $alreadyBorrowed = Borrowing::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved', 'overdue'])
            ->exists();

        if ($alreadyBorrowed) {
            return back()->with('error', 'Kamu sudah meminjam buku ini.');
        }

        // Batas maksimal 3 buku aktif per member
        $activeBorrows = Borrowing::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved', 'overdue'])
            ->count();

        if ($activeBorrows >= 3) {
            return back()->with('error', 'Kamu sudah mencapai batas maksimal 3 peminjaman aktif.');
        }

        Borrowing::create([
            'user_id'     => auth()->id(),
            'book_id'     => $book->id,
            'borrow_date' => now()->toDateString(),
            'due_date'    => now()->addDays(7)->toDateString(),
            'status'      => 'pending',
            'notes'       => $request->notes,
        ]);

        return redirect()->route('member.history')
            ->with('success', 'Pengajuan peminjaman berhasil. Menunggu persetujuan admin.');
    }
}