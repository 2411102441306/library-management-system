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

    public function show(User $member)
    {
        $borrowings = $member->borrowings()
            ->with('book')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.members.show', [
            'user' => $member,
            'borrowings' => $borrowings,
        ]);
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
            'identity_number' => 'nullable|string|max:32|unique:users,identity_number',
            'birth_place' => 'nullable|string|max:120',
            'birth_date'  => 'nullable|date|before:today',
        ]);

        $validated['role']     = 'member';
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.members.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(User $member)
    {
        return view('admin.members.edit', [
            'user' => $member,
        ]);
    }

    public function update(Request $request, User $member)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $member->id,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'identity_number' => 'nullable|string|max:32|unique:users,identity_number,' . $member->id,
            'birth_place' => 'nullable|string|max:120',
            'birth_date'  => 'nullable|date|before:today',
        ]);

        $member->update($validated);

        return redirect()->route('admin.members.index')
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(User $member)
    {
        if ($member->borrowings()->whereIn('status', ['pending', 'approved', 'overdue'])->exists()) {
            return back()->with('error', 'Anggota tidak dapat dihapus karena masih memiliki peminjaman aktif.');
        }

        $member->delete();

        return redirect()->route('admin.members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}