<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class EventReportController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('start_datetime', 'desc')->get();
        return view('admin.reports.events', compact('events'));
    }

    public function show(Request $request, Event $event)
    {
        $categoryOrder = EventRegistration::CATEGORY_ORDER;

        $registrations = EventRegistration::where('event_id', $event->id)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->sort(function ($a, $b) use ($categoryOrder) {
                $orderA = $categoryOrder[$a->category] ?? 99;
                $orderB = $categoryOrder[$b->category] ?? 99;

                if ($orderA !== $orderB) return $orderA - $orderB;

                // Pastor Joan Lafont always first among pastors
                if ($a->category === 'pastor' && $b->category === 'pastor') {
                    $aIsJoan = stripos($a->full_name, 'Joan') !== false;
                    $bIsJoan = stripos($b->full_name, 'Joan') !== false;
                    if ($aIsJoan && !$bIsJoan) return -1;
                    if (!$aIsJoan && $bIsJoan) return 1;
                }

                return strcmp($a->full_name, $b->full_name);
            })->values();

        $filter   = $request->get('filter', 'all');
        $search   = $request->get('search', '');
        $filtered = $registrations;

        if ($filter !== 'all') {
            $filtered = $filtered->where('category', $filter)->values();
        }

        if ($search) {
            $filtered = $filtered->filter(function ($r) use ($search) {
                return stripos($r->full_name, $search) !== false
                    || stripos($r->email, $search) !== false
                    || stripos($r->phone, $search) !== false;
            })->values();
        }

        $stats = [
            'total'    => $registrations->count(),
            'members'  => $registrations->whereIn('category', ['presbyter','pastor','elder','deacon','deaconess','member'])->count(),
            'visitors' => $registrations->where('category', 'visitor')->count(),
        ];

        $categories = EventRegistration::CATEGORIES;

        return view('admin.reports.event-detail', compact(
            'event', 'registrations', 'filtered', 'stats', 'categories', 'filter', 'search'
        ));
    }
}
