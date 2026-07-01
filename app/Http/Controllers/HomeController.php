<?php

namespace App\Http\Controllers;

use App\Models\Sermon;
use App\Models\Event;
use App\Models\Livestream;
use App\Models\Announcement;

class HomeController extends Controller
{
    public function index()
    {
        $sermons = Sermon::published()
            ->orderBy('sermon_date', 'desc')
            ->limit(3)
            ->get();

        $events = Event::upcoming()
            ->limit(3)
            ->get();

        $livestream = Livestream::active()->latest()->first();

        $announcements = Announcement::active()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('home.index', compact('sermons', 'events', 'livestream', 'announcements'));
    }
}
