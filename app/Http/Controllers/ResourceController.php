<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Support\Facades\Storage;

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
        abort_unless(Storage::disk('public')->exists($resource->file_path), 404);
        return Storage::disk('public')->download($resource->file_path, $resource->title . '.pdf');
    }
}
