<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings', [
            'policy' => AppSetting::borrowingPolicy(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        if ($request->filled('policy_section')) {
            $validated = $request->validate([
                'default_days' => ['required', 'integer', 'min:1', 'max:30'],
                'min_days'     => ['required', 'integer', 'min:1', 'max:30'],
                'max_days'     => ['required', 'integer', 'min:1', 'max:30'],
                'daily_fine'   => ['required', 'integer', 'min:0'],
                'lost_fee'     => ['required', 'integer', 'min:0'],
            ]);

            if ($validated['min_days'] > $validated['max_days']) {
                return back()->withErrors([
                    'min_days' => 'Durasi minimum tidak boleh lebih besar dari durasi maksimum.',
                ])->withInput();
            }

            if ($validated['default_days'] < $validated['min_days'] || $validated['default_days'] > $validated['max_days']) {
                return back()->withErrors([
                    'default_days' => 'Durasi default harus berada di antara durasi minimum dan maksimum.',
                ])->withInput();
            }

            AppSetting::setValue('borrowing.default_days', $validated['default_days']);
            AppSetting::setValue('borrowing.min_days', $validated['min_days']);
            AppSetting::setValue('borrowing.max_days', $validated['max_days']);
            AppSetting::setValue('borrowing.daily_fine', $validated['daily_fine']);
            AppSetting::setValue('borrowing.lost_fee', $validated['lost_fee']);

            return back()->with('success', 'Aturan peminjaman berhasil diperbarui.');
        }

        if ($request->filled('profile_section')) {
            $validated = $request->validate([
                'name'  => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            ]);

            $request->user()->update($validated);

            return back()->with('success', 'Profil admin berhasil diperbarui.');
        }

        if ($request->filled('security_section')) {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', 'min:8'],
            ]);

            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            return back()->with('success', 'Password berhasil diubah.');
        }

        return back();
    }
}