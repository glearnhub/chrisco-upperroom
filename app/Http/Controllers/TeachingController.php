<?php

namespace App\Http\Controllers;

use App\Models\Teaching;

class TeachingController extends Controller
{
    public function index()
    {
        $teachings = Teaching::published()->paginate(9);
        return view('teachings.index', compact('teachings'));
    }

    public function show(Teaching $teaching)
    {
        abort_unless($teaching->is_published, 404);
        $related = Teaching::published()->where('id', '!=', $teaching->id)->limit(3)->get();
        return view('teachings.show', compact('teaching', 'related'));
    }
}
