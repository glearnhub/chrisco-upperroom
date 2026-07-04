<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApostleTeachingCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApostleTeachingCategoryController extends Controller
{
    public function index()
    {
        $categories = ApostleTeachingCategory::withCount('teachings')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.apostle.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100|unique:apostle_teaching_categories,name',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        ApostleTeachingCategory::create([
            'name'       => $validated['name'],
            'slug'       => Str::slug($validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, ApostleTeachingCategory $apostleTeachingCategory)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100|unique:apostle_teaching_categories,name,' . $apostleTeachingCategory->id,
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $apostleTeachingCategory->update([
            'name'       => $validated['name'],
            'slug'       => Str::slug($validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(ApostleTeachingCategory $apostleTeachingCategory)
    {
        $apostleTeachingCategory->delete();
        return back()->with('success', 'Category deleted.');
    }
}
