<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $totalBooks = Book::count();
        $activeMembers = User::where('role', 'member')->count();
        $borrowed = Borrowing::whereIn('status', ['approved', 'overdue'])->count();
        $categories = Category::count();

        return view('auth.login', compact('totalBooks', 'activeMembers', 'borrowed', 'categories'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();
        return redirect()->intended(
            $user->isAdmin()
                ? route('admin.dashboard')
                : route('member.catalog')
        );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
