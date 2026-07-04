<?php

namespace App\Http\Controllers;

use App\Models\ApostleTeaching;
use App\Models\ApostleTeachingCategory;

class ApostleTeachingController extends Controller
{
    public function index()
    {
        $categories = ApostleTeachingCategory::orderBy('sort_order')->orderBy('name')->get();
        $categoryId = request('category');

        $teachings = ApostleTeaching::published()
            ->with('category')
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $activeCategory = $categoryId ? $categories->firstWhere('id', $categoryId) : null;

        return view('apostle.index', compact('teachings', 'categories', 'categoryId', 'activeCategory'));
    }

    public function show(ApostleTeaching $apostleTeaching)
    {
        $apostleTeaching->incrementViews();

        $related = ApostleTeaching::published()
            ->where('id', '!=', $apostleTeaching->id)
            ->where('category_id', $apostleTeaching->category_id)
            ->limit(3)
            ->get();

        return view('apostle.show', compact('apostleTeaching', 'related'));
    }
}
