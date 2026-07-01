<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::latest()->paginate(15);
        return view('admin.resources.index', compact('resources'));
    }

    public function create()
    {
        return view('admin.resources.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type'        => 'required|in:book,article',
            'cover_image' => 'nullable|image|max:2048',
            'file'        => 'required|mimes:pdf|max:20480',
            'is_published'=> 'boolean',
        ]);

        $validated['file_path'] = $request->file('file')->store('resources', 'public');

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('resource-covers', 'public');
        }

        $validated['is_published'] = $request->boolean('is_published', true);

        Resource::create($validated);

        return redirect()->route('admin.resources.index')->with('success', 'Resource uploaded successfully.');
    }

    public function edit(Resource $resource)
    {
        return view('admin.resources.edit', compact('resource'));
    }

    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type'        => 'required|in:book,article',
            'cover_image' => 'nullable|image|max:2048',
            'file'        => 'nullable|mimes:pdf|max:20480',
            'is_published'=> 'boolean',
        ]);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($resource->file_path);
            $validated['file_path'] = $request->file('file')->store('resources', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($resource->cover_image) Storage::disk('public')->delete($resource->cover_image);
            $validated['cover_image'] = $request->file('cover_image')->store('resource-covers', 'public');
        }

        $validated['is_published'] = $request->boolean('is_published', true);

        $resource->update($validated);

        return redirect()->route('admin.resources.index')->with('success', 'Resource updated successfully.');
    }

    public function destroy(Resource $resource)
    {
        Storage::disk('public')->delete($resource->file_path);
        if ($resource->cover_image) Storage::disk('public')->delete($resource->cover_image);
        $resource->delete();
        return redirect()->route('admin.resources.index')->with('success', 'Resource deleted.');
    }
}
