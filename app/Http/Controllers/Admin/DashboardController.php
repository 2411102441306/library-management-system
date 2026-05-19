<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks      = Book::count();
        $totalMembers    = User::where('role', 'member')->count();
        $activeBorrows   = Borrowing::whereIn('status', ['approved', 'overdue'])->count();
        $overdueBorrows  = Borrowing::where('status', 'overdue')->count();

        // Update status overdue otomatis
        Borrowing::where('status', 'approved')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        // Data chart 7 hari terakhir
        $chartData = [];
        $chartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->locale('id')->dayName;
            $chartData[]   = Borrowing::whereDate('borrow_date', $date)->count();
        }

        // Peminjaman terbaru (5 data)
        $recentBorrowings = Borrowing::with(['user', 'book'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalMembers',
            'activeBorrows',
            'overdueBorrows',
            'chartData',
            'chartLabels',
            'recentBorrowings'
        ));
    }
}