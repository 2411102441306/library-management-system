<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        Borrowing::refreshOverdueStatuses();

        $query = Borrowing::with(['user', 'book']);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($nested) use ($search) {
                $nested->whereHas('user', fn($q) =>
                    $q->where('name', 'like', '%' . $search . '%')
                )->orWhereHas('book', fn($q) =>
                    $q->where('title', 'like', '%' . $search . '%')
                );
            });
        }

        $borrowings = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'all'      => Borrowing::count(),
            'pending'  => Borrowing::where('status', 'pending')->count(),
            'approved' => Borrowing::where('status', 'approved')->count(),
            'overdue'  => Borrowing::where('status', 'overdue')->count(),
            'lost'     => Borrowing::where('status', 'lost')->count(),
            'returned' => Borrowing::where('status', 'returned')->count(),
            'fine_pending' => Borrowing::whereIn('status', ['returned', 'lost'])
                ->where('penalty_amount', '>', 0)
                ->whereNull('fine_settled_at')
                ->count(),
            'fine_settled' => Borrowing::whereIn('status', ['returned', 'lost'])
                ->where('penalty_amount', '>', 0)
                ->whereNotNull('fine_settled_at')
                ->count(),
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

        $borrowing->update([
            'status'      => 'approved',
            'borrow_date' => now()->toDateString(),
            'due_date'    => now()->addDays($borrowing->loan_days ?: AppSetting::borrowingPolicy()['default_days'])->toDateString(),
        ]);

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
            'penalty_amount' => $borrowing->days_late * AppSetting::borrowingPolicy()['daily_fine'],
        ]);

        // Kembalikan stok buku
        $borrowing->book->increment('stock');

        return back()->with('success', 'Buku berhasil ditandai dikembalikan.');
    }

    public function markLost(Borrowing $borrowing)
    {
        if (!in_array($borrowing->status, ['approved', 'overdue'])) {
            return back()->with('error', 'Status peminjaman tidak valid untuk ditandai hilang.');
        }

        $borrowing->update([
            'status'         => 'lost',
            'penalty_amount' => AppSetting::borrowingPolicy()['lost_fee'],
        ]);

        return back()->with('success', 'Buku berhasil ditandai hilang dan denda tetap sudah dihitung.');
    }

    public function settleFine(Borrowing $borrowing)
    {
        if (!in_array($borrowing->status, ['returned', 'lost'], true)) {
            return back()->with('error', 'Denda hanya bisa dilunasi untuk peminjaman yang sudah selesai atau hilang.');
        }

        if ($borrowing->fine_amount <= 0) {
            return back()->with('error', 'Peminjaman ini tidak memiliki denda.');
        }

        if (!$borrowing->hasFineProof()) {
            return back()->with('error', 'Bukti pembayaran belum diunggah member.');
        }

        if ($borrowing->isFineSettled()) {
            return back()->with('info', 'Denda sudah pernah ditandai lunas.');
        }

        $borrowing->update([
            'fine_settled_at' => now(),
        ]);

        return back()->with('success', 'Denda berhasil ditandai lunas.');
    }

    public function viewFineProof(Borrowing $borrowing)
    {
        if (!$borrowing->hasFineProof()) {
            return back()->with('error', 'Bukti pembayaran belum tersedia.');
        }

        return response()->file(Storage::disk('public')->path($borrowing->fine_proof_path));
    }
}