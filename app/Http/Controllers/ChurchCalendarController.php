<?php

namespace App\Http\Controllers;

use App\Models\ChurchCalendarEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChurchCalendarController extends Controller
{
    public function index(Request $request)
    {
        $year  = (int) ($request->year  ?? now()->year);
        $month = (int) ($request->month ?? now()->month);

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::create($year, $m, 1)->format('F');
        }

        $monthStart = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $monthEnd   = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $monthEvents = ChurchCalendarEvent::where('calendar_year', $year)
            ->where('is_published', true)
            ->where(function ($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('start_date', [$monthStart, $monthEnd])
                  ->orWhereBetween('end_date',   [$monthStart, $monthEnd])
                  ->orWhere(function ($q2) use ($monthStart, $monthEnd) {
                      $q2->where('start_date', '<=', $monthStart)
                         ->where('end_date',   '>=', $monthEnd);
                  });
            })
            ->orderBy('start_date')
            ->get();

        $eventsByDate = [];
        foreach ($monthEvents as $event) {
            foreach ($event->dates as $date) {
                if ($date >= $monthStart && $date <= $monthEnd) {
                    $eventsByDate[$date][] = $event;
                }
            }
        }

        return view('calendar.index', compact('year', 'month', 'months', 'monthEvents', 'eventsByDate'));
    }
}
