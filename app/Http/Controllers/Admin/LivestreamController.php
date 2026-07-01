<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Livestream;
use Illuminate\Http\Request;

class LivestreamController extends Controller
{
    public function index()
    {
        $livestreams = Livestream::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.livestreams.index', compact('livestreams'));
    }

    public function create()
    {
        return view('admin.livestreams.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'embed_url'    => 'required|url|max:500',
            'platform'     => 'nullable|string|max:100',
            'is_live'      => 'boolean',
            'scheduled_at' => 'nullable|date',
        ]);

        $validated['is_live'] = $request->boolean('is_live');

        Livestream::create($validated);

        return redirect()->route('admin.livestreams.index')
            ->with('success', 'Livestream created successfully.');
    }

    public function show(Livestream $livestream)
    {
        return view('admin.livestreams.show', compact('livestream'));
    }

    public function edit(Livestream $livestream)
    {
        return view('admin.livestreams.edit', compact('livestream'));
    }

    public function update(Request $request, Livestream $livestream)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'embed_url'    => 'required|url|max:500',
            'platform'     => 'nullable|string|max:100',
            'is_live'      => 'boolean',
            'scheduled_at' => 'nullable|date',
        ]);

        $validated['is_live'] = $request->boolean('is_live');

        $livestream->update($validated);

        return redirect()->route('admin.livestreams.index')
            ->with('success', 'Livestream updated successfully.');
    }

    public function destroy(Livestream $livestream)
    {
        $livestream->delete();

        return redirect()->route('admin.livestreams.index')
            ->with('success', 'Livestream deleted successfully.');
    }

    public function toggleLive(Livestream $livestream)
    {
        // If turning this one live, take all others offline first
        if (!$livestream->is_live) {
            Livestream::where('is_live', true)->update(['is_live' => false]);
        }

        $livestream->update(['is_live' => !$livestream->is_live]);

        $status = $livestream->is_live ? 'now live' : 'taken offline';

        return back()->with('success', "Livestream \"{$livestream->title}\" is {$status}.");
    }
}
