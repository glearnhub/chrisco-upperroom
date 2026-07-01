@extends('layouts.admin')

@section('title', 'Books & Articles')
@section('page-title', 'Books & Articles')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">PDF Books & Articles</h1>
    <a href="{{ route('admin.resources.create') }}" class="btn-red px-4 py-2 text-sm">
        <i class="fas fa-plus mr-2"></i>Upload Resource
    </a>
</div>

@if($resources->count())
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Title</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Author</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Date</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($resources as $resource)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center space-x-3">
                        @if($resource->cover_image)
                            <img src="{{ Storage::url($resource->cover_image) }}" class="w-10 h-10 rounded object-cover flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded flex items-center justify-center flex-shrink-0" style="background: #0a1f44;">
                                <i class="fas fa-file-pdf text-yellow-400 text-xs"></i>
                            </div>
                        @endif
                        <span class="font-medium text-gray-800">{{ Str::limit($resource->title, 45) }}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $resource->author ?: '—' }}</td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $resource->type === 'book' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ $resource->type_label }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $resource->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $resource->is_published ? 'Published' : 'Hidden' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $resource->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('resources.download', $resource) }}" target="_blank"
                           class="text-blue-500 hover:text-blue-700 text-xs" title="Download PDF">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="{{ route('admin.resources.edit', $resource) }}"
                           class="text-yellow-600 hover:text-yellow-800 text-xs" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.resources.destroy', $resource) }}"
                              onsubmit="return confirm('Delete this resource?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-4 py-3">{{ $resources->links() }}</div>
</div>
@else
    <div class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
        <i class="fas fa-book text-4xl mb-3"></i>
        <p>No resources yet. <a href="{{ route('admin.resources.create') }}" class="text-red-600 underline">Upload one.</a></p>
    </div>
@endif

@endsection


