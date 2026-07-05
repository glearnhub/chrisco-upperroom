<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('user')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $categories = Announcement::CATEGORIES;
        return view('admin.announcements.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|in:' . implode(',', array_keys(Announcement::CATEGORIES)),
            'body'         => 'required|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_published'  => 'boolean',
            'is_pinned'     => 'boolean',
            'is_recurring'  => 'boolean',
            'expires_at'    => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        $validated['is_published']  = $request->boolean('is_published');
        $validated['is_pinned']     = $request->boolean('is_pinned');
        $validated['is_recurring']  = $request->boolean('is_recurring');
        $validated['user_id']      = auth()->id();

        Announcement::create($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function show(Announcement $announcement)
    {
        return view('admin.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        $categories = Announcement::CATEGORIES;
        return view('admin.announcements.edit', compact('announcement', 'categories'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|in:' . implode(',', array_keys(Announcement::CATEGORIES)),
            'body'         => 'required|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_published'  => 'boolean',
            'is_pinned'     => 'boolean',
            'is_recurring'  => 'boolean',
            'expires_at'    => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        } elseif ($request->boolean('remove_image') && $announcement->image) {
            Storage::disk('public')->delete($announcement->image);
            $validated['image'] = null;
        }

        $validated['is_published']  = $request->boolean('is_published');
        $validated['is_pinned']     = $request->boolean('is_pinned');
        $validated['is_recurring']  = $request->boolean('is_recurring');

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->image) {
            Storage::disk('public')->delete($announcement->image);
        }

        SystemLog::record('delete', 'Announcements', "Announcement \"{$announcement->title}\" deleted.");
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    public function toggle(Announcement $announcement)
    {
        $announcement->update(['is_published' => !$announcement->is_published]);

        return back()->with('success',
            $announcement->is_published ? 'Announcement activated.' : 'Announcement deactivated.');
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'integer|exists:announcements,id',
            'action' => 'required|in:activate,deactivate,reset_expiry',
        ]);

        $announcements = Announcement::whereIn('id', $request->ids)->get();

        foreach ($announcements as $ann) {
            match ($request->action) {
                'activate'     => $ann->update(['is_published' => true]),
                'deactivate'   => $ann->update(['is_published' => false]),
                'reset_expiry' => $ann->update(['expires_at' => null, 'is_published' => true]),
            };
        }

        $label = match ($request->action) {
            'activate'     => 'activated',
            'deactivate'   => 'deactivated',
            'reset_expiry' => 'reactivated (expiry cleared)',
        };

        return back()->with('success', count($request->ids) . ' announcement(s) ' . $label . '.');
    }
}
