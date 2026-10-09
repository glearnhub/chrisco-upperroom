<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ForcePasswordChangeController extends Controller
{
    public function show()
    {
        return view('admin.auth.force-password-change');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()],
        ]);

        $user = auth()->user();

        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Your new password must be different from the current one.']);
        }

        $user->update([
            'password'             => bcrypt($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Password updated successfully. Welcome!');
    }
}
