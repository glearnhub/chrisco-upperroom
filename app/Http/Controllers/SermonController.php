<?php

namespace App\Http\Controllers;

use App\Models\Sermon;

class SermonController extends Controller
{
    public function index()
    {
        $sermons = Sermon::published()
            ->orderBy('sermon_date', 'desc')
            ->paginate(12);

        return view('sermons.index', compact('sermons'));
    }

    public function show(Sermon $sermon)
    {
        $sermon->incrementViews();

        $related = Sermon::published()
            ->where('id', '!=', $sermon->id)
            ->where('series', $sermon->series)
            ->limit(3)
            ->get();

        return view('sermons.show', compact('sermon', 'related'));
    }
}
