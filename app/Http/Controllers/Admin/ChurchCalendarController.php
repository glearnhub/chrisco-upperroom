<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChurchCalendarEvent;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChurchCalendarController extends Controller
{
    public function index(Request $request)
    {
        $year  = (int) ($request->year ?? now()->year);
        $month = (int) ($request->month ?? now()->month);

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::create($year, $m, 1)->format('F');
        }

        // Events that touch this month
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $monthEnd   = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $monthEvents = ChurchCalendarEvent::where('calendar_year', $year)
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

        // Group events by date string for calendar cell rendering
        $eventsByDate = [];
        foreach ($monthEvents as $event) {
            foreach ($event->dates as $date) {
                if ($date >= $monthStart && $date <= $monthEnd) {
                    $eventsByDate[$date][] = $event;
                }
            }
        }

        return view('admin.calendar.index', compact('year', 'month', 'months', 'monthEvents', 'eventsByDate'));
    }

    public function create()
    {
        $categories = ChurchCalendarEvent::categories();
        $years = range(now()->year - 1, now()->year + 3);
        return view('admin.calendar.create', compact('categories', 'years'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'category'      => 'required|in:' . implode(',', array_keys(ChurchCalendarEvent::categories())),
            'color'         => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'notes'         => 'nullable|string',
            'calendar_year' => 'required|integer|min:2000|max:2100',
            'is_published'  => 'boolean',
        ]);

        $data['is_published'] = $request->input('is_published', '1') === '1';

        ChurchCalendarEvent::create($data);

        return redirect()->route('admin.calendar.index', ['year' => $data['calendar_year']])
            ->with('success', 'Calendar event added successfully.');
    }

    public function edit(ChurchCalendarEvent $calendar)
    {
        $categories = ChurchCalendarEvent::categories();
        $years = range(now()->year - 1, now()->year + 3);
        return view('admin.calendar.edit', compact('calendar', 'categories', 'years'));
    }

    public function update(Request $request, ChurchCalendarEvent $calendar)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'category'      => 'required|in:' . implode(',', array_keys(ChurchCalendarEvent::categories())),
            'color'         => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'notes'         => 'nullable|string',
            'calendar_year' => 'required|integer|min:2000|max:2100',
            'is_published'  => 'boolean',
        ]);

        $data['is_published'] = $request->input('is_published', '1') === '1';

        $calendar->update($data);

        return redirect()->route('admin.calendar.index', ['year' => $data['calendar_year']])
            ->with('success', 'Calendar event updated.');
    }

    public function toggleVisibility(ChurchCalendarEvent $calendar)
    {
        $calendar->update(['is_published' => !$calendar->is_published]);
        $status = $calendar->is_published ? 'Public' : 'Private';
        return redirect()->route('admin.calendar.index', [
            'year'  => $calendar->calendar_year,
            'month' => $calendar->start_date->month,
        ])->with('success', "\"{$calendar->title}\" is now {$status}.");
    }

    public function destroy(ChurchCalendarEvent $calendar)
    {
        $year = $calendar->calendar_year;
        $calendar->delete();
        return redirect()->route('admin.calendar.index', ['year' => $year])
            ->with('success', 'Event removed from calendar.');
    }

    public function publishEventForm(ChurchCalendarEvent $calendar)
    {
        return view('admin.calendar.publish-event', compact('calendar'));
    }

    public function publishEventStore(Request $request, ChurchCalendarEvent $calendar)
    {
        $request->validate([
            'poster'                => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'description'           => 'nullable|string',
            'location'              => 'nullable|string|max:255',
            'start_time'            => 'nullable|string|max:10',
            'end_time'              => 'nullable|string|max:10',
            'registration_required' => 'nullable|string',
            'capacity'              => 'nullable|integer|min:1|max:99999',
        ]);

        $imagePath = $request->file('poster')->store('events', 'public');

        $startDatetime = Carbon::parse($calendar->start_date->toDateString()
            . ' ' . ($request->start_time ?: '08:00'));

        $endDatetime = $calendar->end_date
            ? Carbon::parse($calendar->end_date->toDateString() . ' ' . ($request->end_time ?: '17:00'))
            : Carbon::parse($calendar->start_date->toDateString() . ' ' . ($request->end_time ?: '17:00'));

        $registrationRequired = $request->input('registration_required') === '1';

        Event::create([
            'title'                 => $calendar->title,
            'description'           => $request->description ?: $calendar->notes,
            'location'              => $request->location,
            'start_datetime'        => $startDatetime,
            'end_datetime'          => $endDatetime,
            'image'                 => $imagePath,
            'status'                => 'upcoming',
            'registration_required' => $registrationRequired,
            'capacity'              => $registrationRequired ? $request->capacity : null,
        ]);

        return redirect()->route('admin.calendar.index', [
            'year'  => $calendar->calendar_year,
            'month' => $calendar->start_date->month,
        ])->with('success', "\"{$calendar->title}\" has been published as a public Event.");
    }

    public function print(Request $request)
    {
        $year   = (int) ($request->year ?? now()->year);
        $month  = $request->month ? (int) $request->month : null;

        $query = ChurchCalendarEvent::where('calendar_year', $year)->orderBy('start_date');
        if ($month) {
            $start = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $end   = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end]);
            });
        }

        $events = $query->get();

        $eventsByDate = [];
        foreach ($events as $event) {
            foreach ($event->dates as $date) {
                $eventsByDate[$date][] = $event;
            }
        }

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::create($year, $m, 1)->format('F');
        }

        $selectedMonth = $month;

        return view('admin.calendar.print', compact('year', 'selectedMonth', 'events', 'months', 'eventsByDate'));
    }
}
