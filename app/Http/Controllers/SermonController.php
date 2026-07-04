<?php

namespace App\Http\Controllers;

use App\Models\Sermon;

class SermonController extends Controller
{
    public function index()
    {
        $category = request('category');
        $categories = \App\Models\Sermon::CATEGORIES;

        $sermons = Sermon::published()
            ->when($category && $category !== 'all', fn($q) => $q->where('category', $category))
            ->orderBy('sermon_date', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('sermons.index', compact('sermons', 'categories', 'category'));
    }

    public function show(Sermon $sermon)
    {
        $sermon->incrementViews();

        $related = Sermon::published()
            ->where('id', '!=', $sermon->id)
            ->where('category', $sermon->category)
            ->limit(3)
            ->get();

        return view('sermons.show', compact('sermon', 'related'));
    }
}
