<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teaching;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeachingController extends Controller
{
    public function index()
    {
        $teachings = Teaching::latest()->paginate(15);
        return view('admin.teachings.index', compact('teachings'));
    }

    public function create()
    {
        return view('admin.teachings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'pastor_name' => 'required|string|max:255',
            'content'     => 'required|string',
            'category'    => 'nullable|string|max:100',
            'image'       => 'nullable|image|max:2048',
            'is_published'=> 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('teachings', 'public');
        }

        $validated['is_published'] = $request->boolean('is_published', true);

        Teaching::create($validated);

        return redirect()->route('admin.teachings.index')->with('success', 'Teaching published successfully.');
    }

    public function edit(Teaching $teaching)
    {
        return view('admin.teachings.edit', compact('teaching'));
    }

    public function update(Request $request, Teaching $teaching)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'pastor_name' => 'required|string|max:255',
            'content'     => 'required|string',
            'category'    => 'nullable|string|max:100',
            'image'       => 'nullable|image|max:2048',
            'is_published'=> 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($teaching->image) Storage::disk('public')->delete($teaching->image);
            $validated['image'] = $request->file('image')->store('teachings', 'public');
        }

        $validated['is_published'] = $request->boolean('is_published', true);

        $teaching->update($validated);

        return redirect()->route('admin.teachings.index')->with('success', 'Teaching updated successfully.');
    }

    public function destroy(Teaching $teaching)
    {
        if ($teaching->image) Storage::disk('public')->delete($teaching->image);
        $teaching->delete();
        return redirect()->route('admin.teachings.index')->with('success', 'Teaching deleted.');
    }
}
