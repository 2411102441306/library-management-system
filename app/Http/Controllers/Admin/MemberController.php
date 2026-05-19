<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'member');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status === 'active') {
            $query->whereHas('activeBorrowings');
        }

        $members = $query->withCount('borrowings')->latest()->paginate(9);

        return view('admin.members.index', compact('members'));
    }

    public function show(User $user)
    {
        $borrowings = $user->borrowings()
            ->with('book')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.members.show', compact('user', 'borrowings'));
    }

    public function create()
    {
        return view('admin.members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string',
        ]);

        $validated['role']     = 'member';
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.members.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.members.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $user->update($validated);

        return redirect()->route('admin.members.index')
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->borrowings()->whereIn('status', ['pending', 'approved', 'overdue'])->exists()) {
            return back()->with('error', 'Anggota tidak dapat dihapus karena masih memiliki peminjaman aktif.');
        }

        $user->delete();

        return redirect()->route('admin.members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}