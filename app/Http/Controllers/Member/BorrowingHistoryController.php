<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;

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
            'returned' => Borrowing::where('user_id', auth()->id())->where('status', 'returned')->count(),
        ];

        return view('member.history.index', compact('borrowings', 'counts'));
    }
}