<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        $categories = Announcement::CATEGORIES;

        $query = Announcement::active();

        if ($category && array_key_exists($category, $categories)) {
            $query->where('category', $category);
        }

        $announcements = $query->paginate(12)->withQueryString();

        return view('announcements.index', compact('announcements', 'categories', 'category'));
    }

    public function show(Announcement $announcement)
    {
        if (!$announcement->is_published) {
            abort(404);
        }
        if ($announcement->expires_at && $announcement->expires_at->isPast()) {
            abort(404);
        }

        return view('announcements.show', compact('announcement'));
    }
}
