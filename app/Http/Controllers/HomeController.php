<?php

namespace App\Http\Controllers;

use App\Models\Sermon;
use App\Models\Event;
use App\Models\Livestream;
use App\Models\Announcement;
use App\Models\ChurchSetting;

class HomeController extends Controller
{
    public function index()
    {
        $sermons = Sermon::published()
            ->orderBy('sermon_date', 'desc')
            ->limit(3)
            ->get();

        $events = Event::upcoming()
            ->orderByRaw("CASE WHEN LOWER(title) LIKE '%sunday service%' THEN 0 ELSE 1 END")
            ->limit(3)
            ->get();

        $livestream = Livestream::active()->latest()->first();

        $announcements = Announcement::active()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $serviceTimes = [];
        for ($i = 1; $i <= 3; $i++) {
            $serviceTimes[] = [
                'name'     => ChurchSetting::get("service_{$i}_name"),
                'subtitle' => ChurchSetting::get("service_{$i}_subtitle"),
                'time'     => ChurchSetting::get("service_{$i}_time"),
                'icon'     => ChurchSetting::get("service_{$i}_icon",  'fas fa-church'),
                'color'    => ChurchSetting::get("service_{$i}_color", '#0a1f44'),
            ];
        }

        $heroImage = ChurchSetting::get('hero_image');

        $cancelledEvents = \App\Models\Event::where('status', 'cancelled')
            ->where('start_datetime', '>=', now()->startOfDay())
            ->orderBy('start_datetime')
            ->get();

        return view('home.index', compact('sermons', 'events', 'livestream', 'announcements', 'serviceTimes', 'heroImage', 'cancelledEvents'));
    }
}
