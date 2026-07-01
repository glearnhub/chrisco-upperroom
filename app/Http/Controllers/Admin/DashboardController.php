<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Sermon;
use App\Models\Event;
use App\Models\Donation;
use App\Models\PrayerRequest;
use App\Models\Announcement;
use App\Models\Livestream;
use App\Models\EventRegistration;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'members'       => User::where('role', 'member')->count(),
            'sermons'       => Sermon::count(),
            'events'        => Event::count(),
            'donations'     => Donation::where('status', 'completed')->count(),
            'total_given'   => Donation::where('status', 'completed')->sum('amount'),
            'prayer_requests' => PrayerRequest::count(),
            'announcements' => Announcement::count(),
            'registrations' => EventRegistration::count(),
            'live_now'      => Livestream::where('is_live', true)->exists(),
        ];

        $recentGivings = Donation::orderBy('created_at', 'desc')->limit(5)->get();

        $recentMembers = User::where('role', 'member')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentPrayers = PrayerRequest::orderBy('created_at', 'desc')->limit(5)->get();

        $totalMembers    = $stats['members'];
        $totalSermons    = $stats['sermons'];
        $upcomingEvents  = $stats['events'];
        $totalGivings    = $stats['total_given'];
        $pendingPrayers  = PrayerRequest::where('status', 'pending')->count();
        $activeLivestream = $stats['live_now'];

        return view('admin.dashboard', compact(
            'stats', 'recentGivings', 'recentMembers', 'recentPrayers',
            'totalMembers', 'totalSermons', 'upcomingEvents',
            'totalGivings', 'pendingPrayers', 'activeLivestream'
        ));
    }
}
