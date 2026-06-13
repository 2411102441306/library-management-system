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
        Borrowing::refreshOverdueStatuses();

        $totalBooks      = Book::count();
        $totalMembers    = User::where('role', 'member')->count();
        $incompleteProfiles = User::where('role', 'member')
            ->where(function ($query) {
                $query->whereNull('identity_number')
                    ->orWhereNull('phone')
                    ->orWhereNull('address')
                    ->orWhereNull('birth_place')
                    ->orWhereNull('birth_date')
                    ->orWhereNull('profile_photo_path');
            })
            ->count();
        $activeBorrows   = Borrowing::whereIn('status', ['approved', 'overdue'])->count();
        $overdueBorrows  = Borrowing::where('status', 'overdue')->count();
        $dueSoonBorrows  = Borrowing::with(['user', 'book'])
            ->where('status', 'approved')
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays(2)->toDateString()])
            ->orderBy('due_date')
            ->take(5)
            ->get();
        $overdueBorrowings = Borrowing::with(['user', 'book'])
            ->where('status', 'overdue')
            ->orderBy('due_date')
            ->take(5)
            ->get();

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
            'incompleteProfiles',
            'activeBorrows',
            'overdueBorrows',
            'chartData',
            'chartLabels',
            'recentBorrowings',
            'dueSoonBorrows'
            , 'overdueBorrowings'
        ));
    }
}