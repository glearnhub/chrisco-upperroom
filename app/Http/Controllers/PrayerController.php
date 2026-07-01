<?php

namespace App\Http\Controllers;

use App\Models\PrayerRequest;
use Illuminate\Http\Request;

class PrayerController extends Controller
{
    public function index()
    {
        $publicRequests = PrayerRequest::public()
            ->where('status', '!=', 'answered')
            ->latest()
            ->paginate(10);

        return view('prayer.index', compact('publicRequests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'required|string|max:20',
            'request'      => 'required|string|max:2000',
            'is_anonymous' => 'boolean',
        ]);

        PrayerRequest::create([
            'user_id'      => auth()->id(),
            'name'         => $validated['name'],
            'email'        => $validated['email'] ?? null,
            'phone'        => $validated['phone'],
            'request'      => $validated['request'],
            'is_anonymous' => $request->boolean('is_anonymous'),
            'is_public'    => false,
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Your prayer request has been submitted. Our team will be praying for you.');
    }
}
