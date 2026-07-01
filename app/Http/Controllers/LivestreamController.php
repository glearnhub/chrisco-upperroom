<?php

namespace App\Http\Controllers;

use App\Models\Livestream;

class LivestreamController extends Controller
{
    public function index()
    {
        $livestream = Livestream::active()->latest()->first()
            ?? Livestream::orderBy('scheduled_at', 'desc')->first();

        return view('livestream.index', compact('livestream'));
    }
}
