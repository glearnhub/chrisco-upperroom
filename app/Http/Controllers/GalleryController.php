<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;

class GalleryController extends Controller
{
    public function index()
    {
        $categories = GalleryItem::published()->pluck('category')->unique()->sort()->values();
        $items = GalleryItem::published()->get()->groupBy('category');
        return view('gallery.index', compact('items', 'categories'));
    }
}
