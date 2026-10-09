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
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMembers = User::where('role', 'member')->count();

        $stats = [
            'members'         => $totalMembers,
            'sermons'         => Sermon::count(),
            'events'          => Event::count(),
            'prayer_requests' => PrayerRequest::count(),
            'announcements'   => Announcement::count(),
            'registrations'   => EventRegistration::count(),
            'live_now'        => Livestream::where('is_live', true)->exists(),
        ];

        // Member breakdown cards
        $memberStats = [
            'total'            => $totalMembers,
            'leaders'          => User::whereIn('office', ['presbyter','pastor','elder','deacon','deaconess'])->count(),
            'presbyters'       => User::where('office', 'presbyter')->count(),
            'pastors'          => User::where('office', 'pastor')->count(),
            'elders'           => User::where('office', 'elder')->count(),
            'deacons'          => User::where('office', 'deacon')->count(),
            'deaconesses'      => User::where('office', 'deaconess')->count(),
            'committed'        => User::where('role', 'member')->where('is_committed_member', true)->count(),
            'in_class'         => User::where('role', 'member')->where('in_commitment_class', true)->count(),
            'men'              => User::where('role', 'member')->where('gender', 'male')->count(),
            'women'            => User::where('role', 'member')->where('gender', 'female')->count(),
            'sunday_school'    => User::where('role', 'member')->where('member_type', 'child')->count(),
            'young_converts'   => User::where('role', 'member')->where('is_committed_member', false)->where('in_commitment_class', false)->count(),
            'not_baptised'     => User::where('role', 'member')->where('is_born_again', true)->where('is_baptized', false)->count(),
            'youths'           => User::where('role', 'member')->whereNotNull('date_of_birth')->whereBetween('date_of_birth', [Carbon::today()->subYears(29), Carbon::today()->subYears(18)])->whereNotIn('marital_status', ['married'])->count(),
            'married'          => User::where('role', 'member')->where('marital_status', 'married')->count(),
            'pearls'           => User::where('role', 'member')->whereNotNull('date_of_birth')->where('date_of_birth', '<=', Carbon::today()->subYears(30))->whereNotIn('marital_status', ['married'])->count(),
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

        // Member demographics — SQL aggregates instead of loading all records
        $genderStats = User::where('role', 'member')
            ->whereNotNull('gender')
            ->selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')
            ->pluck('total', 'gender');

        $today    = Carbon::today();
        $ageBuckets = [
            'Under 18' => [null,          $today->copy()->subYears(18)->addDay()],
            '18–25'    => [$today->copy()->subYears(25), $today->copy()->subYears(18)],
            '26–35'    => [$today->copy()->subYears(35), $today->copy()->subYears(26)->addDay()],
            '36–45'    => [$today->copy()->subYears(45), $today->copy()->subYears(36)->addDay()],
            '46–55'    => [$today->copy()->subYears(55), $today->copy()->subYears(46)->addDay()],
            '56+'      => [null,          $today->copy()->subYears(56)->addDay()],
        ];

        $ageStats = collect($ageBuckets)->map(function ($range, $label) use ($today) {
            $q = User::where('role', 'member')->whereNotNull('date_of_birth');
            if ($label === 'Under 18') {
                $q->where('date_of_birth', '>', $today->copy()->subYears(18));
            } elseif ($label === '56+') {
                $q->where('date_of_birth', '<=', $today->copy()->subYears(56));
            } else {
                $q->whereBetween('date_of_birth', [$range[0], $range[1]]);
            }
            return $q->count();
        });

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
            'topPages', 'geoStats', 'genderStats', 'ageStats',
            'memberStats'
        ));
    }
}
