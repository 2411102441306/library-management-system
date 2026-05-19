<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrowing::with(['user', 'book']);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            )->orWhereHas('book', fn($q) =>
                $q->where('title', 'like', '%' . $request->search . '%')
            );
        }

        // Auto-update overdue
        Borrowing::where('status', 'approved')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        $borrowings = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'all'      => Borrowing::count(),
            'pending'  => Borrowing::where('status', 'pending')->count(),
            'approved' => Borrowing::where('status', 'approved')->count(),
            'overdue'  => Borrowing::where('status', 'overdue')->count(),
            'returned' => Borrowing::where('status', 'returned')->count(),
        ];

        return view('admin.borrowings.index', compact('borrowings', 'counts'));
    }

    public function approve(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Hanya peminjaman berstatus pending yang bisa di-approve.');
        }

        // Cek stok buku
        if ($borrowing->book->stock <= 0) {
            return back()->with('error', 'Stok buku tidak tersedia.');
        }

        $borrowing->update(['status' => 'approved']);

        // Kurangi stok buku
        $borrowing->book->decrement('stock');

        return back()->with('success', 'Peminjaman berhasil di-approve.');
    }

    public function reject(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Hanya peminjaman berstatus pending yang bisa ditolak.');
        }

        $borrowing->update(['status' => 'rejected']);

        return back()->with('success', 'Peminjaman berhasil ditolak.');
    }

    public function markReturned(Borrowing $borrowing)
    {
        if (!in_array($borrowing->status, ['approved', 'overdue'])) {
            return back()->with('error', 'Status peminjaman tidak valid untuk dikembalikan.');
        }

        $borrowing->update([
            'status'      => 'returned',
            'return_date' => now()->toDateString(),
        ]);

        // Kembalikan stok buku
        $borrowing->book->increment('stock');

        return back()->with('success', 'Buku berhasil ditandai dikembalikan.');
    }
}