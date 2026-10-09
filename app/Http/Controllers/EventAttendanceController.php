<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Http\Request;

class EventAttendanceController extends Controller
{
    // GET /attend/event/{event}
    public function show(Event $event)
    {
        $status = $this->checkinStatus($event);
        return view('attendance.event-checkin', compact('event', 'status'));
    }

    // POST /attend/event/{event}/search
    public function search(Request $request, Event $event)
    {
        $request->validate(['q' => 'required|string|min:2|max:100']);

        if ($this->checkinStatus($event) === 'closed') {
            return response()->json(['error' => 'Check-in is closed for this event.'], 422);
        }

        $q = trim($request->q);

        // Search registered attendees first
        $registrations = EventRegistration::where('event_id', $event->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($q) {
                $query->where('full_name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->get(['id', 'full_name', 'phone', 'email', 'category', 'attended', 'attended_at', 'member_id']);

        // Also search members not yet registered (for walk-in suggestion)
        $registeredMemberIds = EventRegistration::where('event_id', $event->id)
            ->whereNotNull('member_id')->pluck('member_id');

        $unregisteredMembers = User::where('role', 'member')
            ->where('is_active', true)
            ->whereNotIn('id', $registeredMemberIds)
            ->where(function ($query) use ($q) {
                $query->whereRaw("CONCAT(name, ' ', COALESCE(middle_name,''), ' ', COALESCE(last_name,'')) LIKE ?", ["%{$q}%"])
                      ->orWhere('phone', 'like', "%{$q}%");
            })
            ->select('id', 'name', 'middle_name', 'last_name', 'phone', 'profile_photo')
            ->limit(4)
            ->get();

        return response()->json([
            'registered'   => $registrations,
            'unregistered' => $unregisteredMembers->map(fn($m) => [
                'id'    => $m->id,
                'name'  => $m->full_name,
                'phone' => $m->phone,
                'photo' => $m->profile_photo ? asset('storage/' . $m->profile_photo) : null,
            ]),
        ]);
    }

    // POST /attend/event/{event}/checkin  — registered person checks in
    public function checkin(Request $request, Event $event)
    {
        $request->validate(['registration_id' => 'required|exists:event_registrations,id']);

        if ($this->checkinStatus($event) === 'closed') {
            return response()->json(['error' => 'Check-in is closed for this event.'], 422);
        }

        $reg = EventRegistration::where('id', $request->registration_id)
            ->where('event_id', $event->id)
            ->where('status', '!=', 'cancelled')
            ->firstOrFail();

        if ($reg->attended) {
            return response()->json(['error' => 'already_checked_in', 'name' => $reg->full_name], 409);
        }

        $reg->update(['attended' => true, 'attended_at' => now()]);

        return response()->json([
            'success' => true,
            'name'    => $reg->full_name,
        ]);
    }

    // POST /attend/event/{event}/walkin  — not registered, register + check in at door
    public function walkin(Request $request, Event $event)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'member_id' => 'nullable|exists:users,id',
        ]);

        if ($this->checkinStatus($event) === 'closed') {
            return response()->json(['error' => 'Check-in is closed for this event.'], 422);
        }

        if ($event->registration_required && $event->isFull()) {
            return response()->json(['error' => 'This event is fully booked.'], 422);
        }

        // Prevent duplicate by phone
        $exists = EventRegistration::where('event_id', $event->id)
            ->where('phone', $request->phone)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'already_registered', 'message' => 'This phone number is already registered.'], 409);
        }

        // Prevent duplicate by member_id
        if ($request->member_id) {
            $exists = EventRegistration::where('event_id', $event->id)
                ->where('member_id', $request->member_id)
                ->where('status', '!=', 'cancelled')
                ->exists();
            if ($exists) {
                return response()->json(['error' => 'already_registered', 'message' => 'This member is already registered.'], 409);
            }
        }

        // Derive category from member record if available
        $category = 'member';
        if ($request->member_id) {
            $member = User::find($request->member_id);
            if ($member) {
                $office = strtolower($member->office ?? '');
                if (str_contains($office, 'presbyter'))      $category = 'presbyter';
                elseif (str_contains($office, 'pastor'))     $category = 'pastor';
                elseif (str_contains($office, 'elder'))      $category = 'elder';
                elseif (str_contains($office, 'deaconess'))  $category = 'deaconess';
                elseif (str_contains($office, 'deacon'))     $category = 'deacon';
            }
        }

        $reg = EventRegistration::create([
            'event_id'   => $event->id,
            'member_id'  => $request->member_id,
            'full_name'  => $request->full_name,
            'phone'      => $request->phone,
            'category'   => $category,
            'status'     => 'registered',
            'attended'   => true,
            'attended_at'=> now(),
        ]);

        return response()->json([
            'success' => true,
            'name'    => $reg->full_name,
        ]);
    }

    // Determine check-in window: 2 hours before start → end_datetime (or start + 8h if no end)
    private function checkinStatus(Event $event): string
    {
        if ($event->status === 'cancelled') return 'closed';

        $now   = now();
        $open  = $event->start_datetime->copy()->subHours(2);
        $close = $event->end_datetime ?? $event->start_datetime->copy()->addHours(8);

        if ($now->lt($open))  return 'not_yet';
        if ($now->gt($close)) return 'closed';

        return 'open';
    }
}
