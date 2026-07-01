<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('start_datetime', 'desc')->paginate(15);
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
            'description'           => 'nullable|string',
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
        $event->load('registrations');
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title'                 => 'required|string|max:255',
            'description'           => 'nullable|string',
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

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
