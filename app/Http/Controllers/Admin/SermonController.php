<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sermon;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SermonController extends Controller
{
    public function index()
    {
        $sermons = Sermon::orderBy('sermon_date', 'desc')->paginate(15);
        return view('admin.sermons.index', compact('sermons'));
    }

    public function create()
    {
        return view('admin.sermons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'speaker'     => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'sermon_date' => 'required|date',
            'category'    => 'nullable|string|in:' . implode(',', array_keys(\App\Models\Sermon::CATEGORIES)),
            'scripture'   => 'nullable|string|max:255',
            'audio_url'   => ['nullable', 'url', 'max:500', 'regex:/^https:\/\//'],
            'video_url'   => ['nullable', 'url', 'max:500', 'regex:/^https:\/\//'],
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|in:published,draft',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')
                ->store('sermons', 'public');
        }

        Sermon::create($validated);

        return redirect()->route('admin.sermons.index')
            ->with('success', 'Sermon created successfully.');
    }

    public function show(Sermon $sermon)
    {
        return view('admin.sermons.show', compact('sermon'));
    }

    public function edit(Sermon $sermon)
    {
        return view('admin.sermons.edit', compact('sermon'));
    }

    public function update(Request $request, Sermon $sermon)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'speaker'     => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'sermon_date' => 'required|date',
            'category'    => 'nullable|string|in:' . implode(',', array_keys(\App\Models\Sermon::CATEGORIES)),
            'scripture'   => 'nullable|string|max:255',
            'audio_url'   => ['nullable', 'url', 'max:500', 'regex:/^https:\/\//'],
            'video_url'   => ['nullable', 'url', 'max:500', 'regex:/^https:\/\//'],
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|in:published,draft',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($sermon->thumbnail) {
                Storage::disk('public')->delete($sermon->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')
                ->store('sermons', 'public');
        }

        $sermon->update($validated);

        return redirect()->route('admin.sermons.index')
            ->with('success', 'Sermon updated successfully.');
    }

    public function destroy(Sermon $sermon)
    {
        if ($sermon->thumbnail) {
            Storage::disk('public')->delete($sermon->thumbnail);
        }

        SystemLog::record('delete', 'Sermons', "Sermon \"{$sermon->title}\" deleted.");
        $sermon->delete();

        return redirect()->route('admin.sermons.index')
            ->with('success', 'Sermon deleted successfully.');
    }
}
