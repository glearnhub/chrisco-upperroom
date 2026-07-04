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
use App\Models\SiteVisit;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'members'         => User::where('role', 'member')->count(),
            'sermons'         => Sermon::count(),
            'events'          => Event::count(),
            'prayer_requests' => PrayerRequest::count(),
            'announcements'   => Announcement::count(),
            'registrations'   => EventRegistration::count(),
            'live_now'        => Livestream::where('is_live', true)->exists(),
        ];

        // Visit stats — unique by session per day
        $totalVisits = SiteVisit::where('is_new_session', true)->count();
        $todayVisits = SiteVisit::where('is_new_session', true)->whereDate('created_at', today())->count();
        $weekVisits  = SiteVisit::where('is_new_session', true)->where('created_at', '>=', now()->subDays(7))->count();
        $yearVisits  = SiteVisit::where('is_new_session', true)->where('created_at', '>=', now()->subDays(365))->count();

        // Top 7 most visited pages
        $topPages = SiteVisit::selectRaw('path, COUNT(*) as hits')
            ->groupBy('path')
            ->orderByDesc('hits')
            ->limit(7)
            ->pluck('hits', 'path');

        // Geo distribution (top 8 countries by unique visitors)
        $geoStats = SiteVisit::where('is_new_session', true)
            ->whereNotNull('country')
            ->selectRaw('country, country_code, COUNT(*) as total')
            ->groupBy('country', 'country_code')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // Member demographics
        $members = User::where('role', 'member')->whereNotNull('gender')->get(['gender', 'date_of_birth']);

        $genderStats = $members->groupBy('gender')->map->count();

        $ageStats = $members->filter(fn($m) => $m->date_of_birth)
            ->groupBy(function ($m) {
                $age = \Carbon\Carbon::parse($m->date_of_birth)->age;
                if ($age < 18)  return 'Under 18';
                if ($age < 26)  return '18–25';
                if ($age < 36)  return '26–35';
                if ($age < 46)  return '36–45';
                if ($age < 56)  return '46–55';
                return '56+';
            })->map->count();

        $ageOrder = ['Under 18', '18–25', '26–35', '36–45', '46–55', '56+'];
        $ageStats = collect($ageOrder)->mapWithKeys(fn($k) => [$k => $ageStats[$k] ?? 0]);

        $recentMembers = User::where('role', 'member')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $totalMembers     = $stats['members'];
        $totalSermons     = $stats['sermons'];
        $upcomingEvents   = $stats['events'];
        $pendingPrayers   = PrayerRequest::where('status', 'pending')->count();
        $activeLivestream = $stats['live_now'];

        return view('admin.dashboard', compact(
            'stats', 'recentMembers',
            'totalMembers', 'totalSermons', 'upcomingEvents',
            'pendingPrayers', 'activeLivestream',
            'totalVisits', 'todayVisits', 'weekVisits', 'yearVisits',
            'topPages', 'geoStats', 'genderStats', 'ageStats'
        ));
    }
}
