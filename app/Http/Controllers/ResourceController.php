<?php

namespace App\Http\Controllers;

use App\Models\Resource;

class ResourceController extends Controller
{
    public function index()
    {
        $books    = Resource::published()->where('type', 'book')->get();
        $articles = Resource::published()->where('type', 'article')->get();
        return view('resources.index', compact('books', 'articles'));
    }

    public function download(Resource $resource)
    {
        abort_unless($resource->is_published, 404);
        return response()->download(storage_path('app/public/' . $resource->file_path), $resource->title . '.pdf');
    }
}
