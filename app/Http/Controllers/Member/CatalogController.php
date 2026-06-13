<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
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
        $user = auth()->user();
        $borrowPolicy = AppSetting::borrowingPolicy();

        // Cek apakah member sudah meminjam buku ini dan belum dikembalikan
        $alreadyBorrowed = Borrowing::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved', 'overdue'])
            ->exists();

        $missingProfileFields = $user->missingBorrowerProfileFields();

        return view('member.catalog.show', compact('book', 'alreadyBorrowed', 'missingProfileFields', 'borrowPolicy'));
    }

    public function borrow(Request $request, Book $book)
    {
        $user = $request->user();
        $borrowPolicy = AppSetting::borrowingPolicy();

        $validated = $request->validate([
            'notes'     => ['nullable', 'string', 'max:1000'],
            'loan_days' => ['nullable', 'integer', 'min:' . $borrowPolicy['min_days'], 'max:' . $borrowPolicy['max_days']],
        ]);
        $loanDays = $validated['loan_days'] ?? $borrowPolicy['default_days'];

        // Validasi stok
        if ($book->stock <= 0) {
            return back()->with('error', 'Stok buku tidak tersedia.');
        }

        if (!$user->hasBorrowerProfile()) {
            return back()->with('error', 'Lengkapi profil dulu: ' . implode(', ', $user->missingBorrowerProfileFields()) . '.');
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
            'due_date'    => now()->addDays($loanDays)->toDateString(),
            'loan_days'   => $loanDays,
            'status'      => 'pending',
            'notes'       => $validated['notes'] ?? null,
        ]);

        return redirect()->route('member.history')
            ->with('success', 'Pengajuan peminjaman berhasil. Menunggu persetujuan admin.');
    }
}