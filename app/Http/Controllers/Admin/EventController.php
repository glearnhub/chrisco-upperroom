<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount('registrations')->orderBy('start_datetime', 'desc')->paginate(15);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                 => 'required|string|max:255',
            'description'           => 'nullable|string|max:10000',
            'location'              => 'nullable|string|max:255',
            'start_datetime'        => 'required|date',
            'end_datetime'          => 'nullable|date|after_or_equal:start_datetime',
            'image'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'capacity'              => 'nullable|integer|min:1',
            'registration_required' => 'boolean',
            'status'                => 'required|in:upcoming,ongoing,past,cancelled',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $validated['registration_required'] = $request->boolean('registration_required');

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        $registrations = $event->registrations()->orderByDesc('created_at')->paginate(50);
        return view('admin.events.show', compact('event', 'registrations'));
    }

    public function checkin(Event $event)
    {
        $now    = now();
        $open   = $event->start_datetime->copy()->subHours(2);
        $close  = $event->end_datetime ?? $event->start_datetime->copy()->addHours(8);
        $isOpen = $now->between($open, $close) && $event->status !== 'cancelled';

        $attended     = $event->registrations()->where('attended', true)->count();
        $total        = $event->registrations()->where('status', '!=', 'cancelled')->count();
        $attendedList = $event->registrations()
            ->where('attended', true)
            ->orderByDesc('attended_at')
            ->select(['id', 'full_name', 'phone', 'category', 'attended_at'])
            ->get();

        return view('admin.events.checkin', compact('event', 'isOpen', 'attended', 'total', 'attendedList'));
    }

    public function checkinSearch(Request $request, Event $event)
    {
        $request->validate(['q' => 'required|string|min:2|max:100']);
        $q = trim($request->q);

        $registered = EventRegistration::where('event_id', $event->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($q) {
                $query->where('full_name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->get(['id', 'full_name', 'phone', 'email', 'category', 'attended', 'attended_at', 'member_id']);

        $registeredMemberIds = EventRegistration::where('event_id', $event->id)
            ->whereNotNull('member_id')->pluck('member_id');

        $unregistered = User::where('role', 'member')
            ->where('is_active', true)
            ->whereNotIn('id', $registeredMemberIds)
            ->where(function ($query) use ($q) {
                $query->whereRaw("CONCAT(name, ' ', COALESCE(middle_name,''), ' ', COALESCE(last_name,'')) LIKE ?", ["%{$q}%"])
                      ->orWhere('phone', 'like', "%{$q}%");
            })
            ->select('id', 'name', 'middle_name', 'last_name', 'phone')
            ->limit(4)->get();

        return response()->json([
            'registered'   => $registered,
            'unregistered' => $unregistered->map(fn($m) => [
                'id'    => $m->id,
                'name'  => trim("{$m->name} {$m->middle_name} {$m->last_name}"),
                'phone' => $m->phone,
            ]),
        ]);
    }

    public function checkinMark(Request $request, Event $event, EventRegistration $registration)
    {
        if ($registration->event_id !== $event->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $attended = $request->boolean('attended');
        $registration->update([
            'attended'    => $attended,
            'attended_at' => $attended ? now() : null,
        ]);

        return response()->json([
            'attended'    => $registration->attended,
            'attended_at' => $registration->attended_at?->format('H:i'),
            'full_name'   => $registration->full_name,
        ]);
    }

    public function checkinWalkin(Request $request, Event $event)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'member_id' => 'nullable|exists:users,id',
        ]);

        $exists = EventRegistration::where('event_id', $event->id)
            ->where('phone', $request->phone)
            ->where('status', '!=', 'cancelled')->exists();
        if ($exists) {
            return response()->json(['error' => 'already_registered', 'message' => 'Phone already registered.'], 409);
        }

        if ($request->member_id) {
            $exists = EventRegistration::where('event_id', $event->id)
                ->where('member_id', $request->member_id)
                ->where('status', '!=', 'cancelled')->exists();
            if ($exists) {
                return response()->json(['error' => 'already_registered', 'message' => 'Member already registered.'], 409);
            }
        }

        $category = 'member';
        if ($request->member_id) {
            $member = User::find($request->member_id);
            if ($member) {
                $office = strtolower($member->office ?? '');
                if (str_contains($office, 'presbyter'))     $category = 'presbyter';
                elseif (str_contains($office, 'pastor'))    $category = 'pastor';
                elseif (str_contains($office, 'elder'))     $category = 'elder';
                elseif (str_contains($office, 'deaconess')) $category = 'deaconess';
                elseif (str_contains($office, 'deacon'))    $category = 'deacon';
            }
        }

        EventRegistration::create([
            'event_id'    => $event->id,
            'member_id'   => $request->member_id,
            'full_name'   => $request->full_name,
            'phone'       => $request->phone,
            'category'    => $category,
            'status'      => 'registered',
            'attended'    => true,
            'attended_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title'                 => 'required|string|max:255',
            'description'           => 'nullable|string|max:10000',
            'location'              => 'nullable|string|max:255',
            'start_datetime'        => 'required|date',
            'end_datetime'          => 'nullable|date|after_or_equal:start_datetime',
            'image'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'capacity'              => 'nullable|integer|min:1',
            'registration_required' => 'boolean',
            'status'                => 'required|in:upcoming,ongoing,past,cancelled',
        ]);

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $validated['registration_required'] = $request->boolean('registration_required');

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        SystemLog::record('delete', 'Events', "Event \"{$event->title}\" deleted.");
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
