<?php

namespace App\Http\Controllers;

use App\Models\Livestream;

class LivestreamController extends Controller
{
    public function index()
    {
        // Show an active (live) stream first; fall back to an upcoming scheduled
        // stream only. Never show a past stream that has already ended.
        $livestream = Livestream::active()->latest()->first()
            ?? Livestream::where('is_live', false)
                ->where('scheduled_at', '>', now())
                ->orderBy('scheduled_at')
                ->first();

        return view('livestream.index', compact('livestream'));
    }
}
