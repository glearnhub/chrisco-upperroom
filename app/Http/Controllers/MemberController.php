<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $donations = $user->donations()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $registrations = $user->eventRegistrations()
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $prayerRequests = $user->prayerRequests()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $totalGiven = $user->donations()->where('status', 'completed')->sum('amount');

        return view('member.dashboard', compact('user', 'donations', 'registrations', 'prayerRequests', 'totalGiven'));
    }
}
