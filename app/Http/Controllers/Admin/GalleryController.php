<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $items = GalleryItem::orderBy('sort_order')->orderByDesc('created_at')->paginate(20);
        return view('admin.gallery.index', compact('items'));
    }

    public function create()
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'images.*'   => 'required|image|max:5120',
            'title'      => 'nullable|string|max:255',
            'caption'    => 'nullable|string|max:500',
            'category'   => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        foreach ($request->file('images', []) as $file) {
            GalleryItem::create([
                'title'        => $request->title,
                'caption'      => $request->caption,
                'category'     => $request->category,
                'sort_order'   => $request->sort_order ?? 0,
                'is_published' => $request->boolean('is_published', true),
                'image'        => $file->store('gallery', 'public'),
            ]);
        }

        return redirect()->route('admin.gallery.index')->with('success', 'Photo(s) uploaded successfully.');
    }

    public function edit(GalleryItem $gallery)
    {
        return view('admin.gallery.edit', compact('gallery'));
    }

    public function update(Request $request, GalleryItem $gallery)
    {
        $request->validate([
            'title'      => 'nullable|string|max:255',
            'caption'    => 'nullable|string|max:500',
            'category'   => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
            'image'      => 'nullable|image|max:5120',
        ]);

        $data = [
            'title'        => $request->title,
            'caption'      => $request->caption,
            'category'     => $request->category,
            'sort_order'   => $request->sort_order ?? 0,
            'is_published' => $request->boolean('is_published', true),
        ];

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($gallery->image);
            $data['image'] = $request->file('image')->store('gallery', 'public');
        }

        $gallery->update($data);

        return redirect()->route('admin.gallery.index')->with('success', 'Photo updated.');
    }

    public function destroy(GalleryItem $gallery)
    {
        Storage::disk('public')->delete($gallery->image);
        SystemLog::record('delete', 'Gallery', "Photo \"{$gallery->title}\" deleted.");
        $gallery->delete();
        return redirect()->route('admin.gallery.index')->with('success', 'Photo deleted.');
    }
}
