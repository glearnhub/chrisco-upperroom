<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::upcoming()->paginate(9);
        return view('events.index', compact('events'));
    }

    public function show(Event $event)
    {
        $event->load('registrations');
        $registrationCount = $event->registrations()->where('status', '!=', 'cancelled')->count();
        return view('events.show', compact('event', 'registrationCount'));
    }

    /**
     * AJAX: look up a member by email, return their details for form pre-fill.
     */
    public function lookupEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['found' => false]);
        }

        // Map the user's office/role to a registration category
        $category = $this->deriveCategory($user);

        return response()->json([
            'found'     => true,
            'full_name' => $user->full_name,
            'phone'     => $user->phone ?? '',
            'email'     => $user->email,
            'category'  => $category,
            'member_id' => $user->id,
        ]);
    }

    public function register(Request $request, Event $event)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'email'     => 'nullable|email|max:255',
            'category'  => 'required|in:presbyter,pastor,elder,deacon,deaconess,member,visitor',
            'member_id' => 'nullable|exists:users,id',
        ]);

        // Prevent duplicate registration by email for the same event
        if (!empty($validated['email'])) {
            $exists = EventRegistration::where('event_id', $event->id)
                ->where('email', $validated['email'])
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($exists) {
                return back()->with('error', 'This email is already registered for this event.');
            }
        }

        // Also prevent duplicate by member_id
        if (!empty($validated['member_id'])) {
            $exists = EventRegistration::where('event_id', $event->id)
                ->where('member_id', $validated['member_id'])
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($exists) {
                return back()->with('error', 'This member is already registered for this event.');
            }
        }

        if ($event->registration_required && $event->isFull()) {
            return back()->with('error', 'Sorry, this event is fully booked.');
        }

        EventRegistration::create([
            'event_id'  => $event->id,
            'user_id'   => auth()->id() ?? null,
            'member_id' => $validated['member_id'] ?? null,
            'full_name' => $validated['full_name'],
            'phone'     => $validated['phone'],
            'email'     => $validated['email'] ?? null,
            'category'  => $validated['category'],
            'status'    => 'registered',
        ]);

        return back()->with('success', 'You have been successfully registered for ' . $event->title . '!');
    }

    private function deriveCategory(User $user): string
    {
        $office = strtolower($user->office ?? '');
        $role   = strtolower($user->role ?? '');

        if (str_contains($office, 'presbyter')) return 'presbyter';
        if (str_contains($office, 'pastor') || str_contains($role, 'pastor')) return 'pastor';
        if (str_contains($office, 'elder')) return 'elder';
        if (str_contains($office, 'deaconess')) return 'deaconess';
        if (str_contains($office, 'deacon')) return 'deacon';
        if ($user->role === 'member') return 'member';

        return 'member';
    }
}
