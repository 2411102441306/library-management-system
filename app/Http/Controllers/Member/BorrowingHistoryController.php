<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BorrowingHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrowing::with('book')
            ->where('user_id', auth()->id());

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $borrowings = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'all'      => Borrowing::where('user_id', auth()->id())->count(),
            'pending'  => Borrowing::where('user_id', auth()->id())->where('status', 'pending')->count(),
            'approved' => Borrowing::where('user_id', auth()->id())->where('status', 'approved')->count(),
            'overdue'  => Borrowing::where('user_id', auth()->id())->where('status', 'overdue')->count(),
            'lost'     => Borrowing::where('user_id', auth()->id())->where('status', 'lost')->count(),
            'returned' => Borrowing::where('user_id', auth()->id())->where('status', 'returned')->count(),
            'fine_pending' => Borrowing::where('user_id', auth()->id())
                ->whereIn('status', ['returned', 'lost'])
                ->where('penalty_amount', '>', 0)
                ->whereNull('fine_settled_at')
                ->count(),
            'fine_settled' => Borrowing::where('user_id', auth()->id())
                ->whereIn('status', ['returned', 'lost'])
                ->where('penalty_amount', '>', 0)
                ->whereNotNull('fine_settled_at')
                ->count(),
        ];

        return view('member.history.index', compact('borrowings', 'counts'));
    }

    public function uploadFineProof(Request $request, Borrowing $borrowing)
    {
        abort_unless($borrowing->user_id === auth()->id(), 403);

        if (!in_array($borrowing->status, ['returned', 'lost'], true)) {
            return back()->with('error', 'Bukti denda hanya bisa dikirim untuk pinjaman yang sudah selesai atau hilang.');
        }

        if ($borrowing->fine_amount <= 0) {
            return back()->with('error', 'Peminjaman ini tidak memiliki denda.');
        }

        if ($borrowing->isFineSettled()) {
            return back()->with('error', 'Denda sudah lunas, bukti baru tidak diperlukan.');
        }

        $request->validate([
            'fine_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        if ($borrowing->fine_proof_path) {
            Storage::disk('public')->delete($borrowing->fine_proof_path);
        }

        $borrowing->update([
            'fine_proof_path' => $request->file('fine_proof')->storePublicly('fine-proofs', 'public'),
            'fine_proof_submitted_at' => now(),
        ]);

        return back()->with('success', 'Bukti pembayaran denda berhasil dikirim ke admin.');
    }
}