<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->period ?? '6months';

        $startDate = match($period) {
            '7days'   => now()->subDays(7),
            '30days'  => now()->subDays(30),
            '1year'   => now()->subYear(),
            default   => now()->subMonths(6),
        };

        // Summary cards
        $totalBorrowings    = Borrowing::where('borrow_date', '>=', $startDate)->count();
        $totalMembers       = User::where('role', 'member')->count();
        $returnedOnTime     = Borrowing::where('status', 'returned')
                                ->where('borrow_date', '>=', $startDate)
                                ->whereColumn('return_date', '<=', 'due_date')
                                ->count();
        $totalReturned      = Borrowing::where('status', 'returned')
                                ->where('borrow_date', '>=', $startDate)
                                ->count();
        $onTimePercentage   = $totalReturned > 0
                                ? round(($returnedOnTime / $totalReturned) * 100)
                                : 0;
        $overdueBorrowings  = Borrowing::where('status', 'overdue')->count();

        // Chart data — peminjaman per bulan (6 bulan terakhir)
        $chartLabels = [];
        $chartData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $month         = now()->subMonths($i);
            $chartLabels[] = $month->locale('id')->monthName;
            $chartData[]   = Borrowing::whereYear('borrow_date', $month->year)
                                ->whereMonth('borrow_date', $month->month)
                                ->count();
        }

        // Distribusi kategori
        $categoryStats = Category::withCount(['books as borrow_count' => function ($q) use ($startDate) {
            $q->whereHas('borrowings', fn($b) => $b->where('borrow_date', '>=', $startDate));
        }])->orderByDesc('borrow_count')->get();

        // Top 10 buku terpopuler
        $topBooks = Book::withCount(['borrowings as total_borrowed' => function ($q) use ($startDate) {
            $q->where('borrow_date', '>=', $startDate);
        }])->with('category')
          ->orderByDesc('total_borrowed')
          ->take(10)
          ->get();

        return view('admin.reports.index', compact(
            'period',
            'totalBorrowings',
            'totalMembers',
            'onTimePercentage',
            'overdueBorrowings',
            'chartLabels',
            'chartData',
            'categoryStats',
            'topBooks'
        ));
    }
}