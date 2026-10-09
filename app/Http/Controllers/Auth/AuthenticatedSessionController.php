<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\SystemLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        SystemLog::record('login', 'Auth', 'User logged in.');

        $user = Auth::user();

        // Follow-up team members only have attendance.followup permission — send them
        // straight to the follow-up team view instead of the dashboard (which would 403).
        if ($user->hasPermission('attendance.followup') && !$user->hasPermission('dashboard.view')) {
            return redirect()->route('admin.attendance.followup.team');
        }

        return redirect()->route('admin.dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        SystemLog::record('logout', 'Auth', 'User logged out.');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
