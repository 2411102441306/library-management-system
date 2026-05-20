<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Member;

// ─── Root redirect berdasarkan role ────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('member.catalog');
    }
    return redirect()->route('login');
});

// ─── Auth routes (bawaan Breeze) ───────────────────────────
require __DIR__ . '/auth.php';

// ─── ADMIN routes ──────────────────────────────────────────
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])
            ->name('dashboard');

        // API Pencarian Buku Google Books
        Route::get('/books/search-api', [Admin\BookController::class, 'searchGoogleBooks'])
            ->name('books.search-api');

        // Buku
        Route::resource('books', Admin\BookController::class);

        // Anggota
        Route::resource('members', Admin\MemberController::class);

        // Peminjaman
        Route::get('/borrowings', [Admin\BorrowingController::class, 'index'])
            ->name('borrowings.index');
        Route::patch('/borrowings/{borrowing}/approve', [Admin\BorrowingController::class, 'approve'])
            ->name('borrowings.approve');
        Route::patch('/borrowings/{borrowing}/reject', [Admin\BorrowingController::class, 'reject'])
            ->name('borrowings.reject');
        Route::patch('/borrowings/{borrowing}/return', [Admin\BorrowingController::class, 'markReturned'])
            ->name('borrowings.return');

        // Kategori
        Route::get('/categories', [Admin\CategoryController::class, 'index'])
            ->name('categories.index');
        Route::post('/categories', [Admin\CategoryController::class, 'store'])
            ->name('categories.store');
        Route::put('/categories/{category}', [Admin\CategoryController::class, 'update'])
            ->name('categories.update');
        Route::delete('/categories/{category}', [Admin\CategoryController::class, 'destroy'])
            ->name('categories.destroy');

        // Laporan
        Route::get('/reports', [Admin\ReportController::class, 'index'])
            ->name('reports.index');

        // Pengaturan
        Route::get('/settings', fn() => view('admin.settings'))
            ->name('settings');
    });

// ─── MEMBER routes ─────────────────────────────────────────
Route::prefix('member')
    ->middleware(['auth', 'role:member'])
    ->name('member.')
    ->group(function () {

        // Katalog Buku
        Route::get('/catalog', [Member\CatalogController::class, 'index'])
            ->name('catalog');
        Route::get('/catalog/{book}', [Member\CatalogController::class, 'show'])
            ->name('catalog.show');
        Route::post('/catalog/{book}/borrow', [Member\CatalogController::class, 'borrow'])
            ->name('catalog.borrow');

        // Riwayat Peminjaman
        Route::get('/history', [Member\BorrowingHistoryController::class, 'index'])
            ->name('history');
    });