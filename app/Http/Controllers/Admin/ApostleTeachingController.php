<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApostleTeaching;
use App\Models\ApostleTeachingCategory;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApostleTeachingController extends Controller
{
    public function index()
    {
        $teachings = ApostleTeaching::with('category')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.apostle.index', compact('teachings'));
    }

    public function create()
    {
        $categories = ApostleTeachingCategory::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.apostle.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'youtube_url' => ['required','url','max:500','regex:/^https:\/\/(www\.)?(youtube\.com|youtu\.be)\//'],
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'category_id' => 'nullable|exists:apostle_teaching_categories,id',
            'status'      => 'required|in:published,draft',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('apostle', 'public');
        }

        ApostleTeaching::create($validated);

        return redirect()->route('admin.apostle.index')->with('success', 'Teaching added successfully.');
    }

    public function edit(ApostleTeaching $apostleTeaching)
    {
        $categories = ApostleTeachingCategory::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.apostle.edit', compact('apostleTeaching', 'categories'));
    }

    public function update(Request $request, ApostleTeaching $apostleTeaching)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'youtube_url' => ['required','url','max:500','regex:/^https:\/\/(www\.)?(youtube\.com|youtu\.be)\//'],
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'category_id' => 'nullable|exists:apostle_teaching_categories,id',
            'status'      => 'required|in:published,draft',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($apostleTeaching->thumbnail) {
                Storage::disk('public')->delete($apostleTeaching->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('apostle', 'public');
        }

        $apostleTeaching->update($validated);

        return redirect()->route('admin.apostle.index')->with('success', 'Teaching updated successfully.');
    }

    public function destroy(ApostleTeaching $apostleTeaching)
    {
        if ($apostleTeaching->thumbnail) {
            Storage::disk('public')->delete($apostleTeaching->thumbnail);
        }
        SystemLog::record('delete', 'Apostle Teachings', "Teaching \"{$apostleTeaching->title}\" deleted.");
        $apostleTeaching->delete();
        return redirect()->route('admin.apostle.index')->with('success', 'Teaching deleted.');
    }
}
