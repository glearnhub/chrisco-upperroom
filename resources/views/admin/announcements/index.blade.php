@extends('layouts.admin')

@section('title', 'Announcements')
@section('page-title', 'Announcements')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Announcements</h1>
    <a href="{{ route('admin.announcements.create') }}" class="btn-red px-4 py-2 text-sm">
        <i class="fas fa-plus mr-2"></i>Add Announcement
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        @if($announcements->count())
            <table class="w-full text-sm">
                <thead style="background: #0a1f44;">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Published</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Expires At</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Author</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($announcements as $ann)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $ann->title }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full {{ $ann->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $ann->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $ann->expires_at ? \Carbon\Carbon::parse($ann->expires_at)->format('M d, Y') : 'No Expiry' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $ann->user->name ?? 'Admin' }}</td>
                            <td class="px-4 py-3 flex items-center space-x-2">
                                <a href="{{ route('admin.announcements.edit', $ann) }}"
                                    class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}"
                                    onsubmit="return confirm('Delete this announcement?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded text-xs font-medium">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3">{{ $announcements->links() }}</div>
        @else
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-bullhorn text-5xl mb-3"></i>
                <p>No announcements yet. <a href="{{ route('admin.announcements.create') }}" class="text-blue-600 underline">Create one</a>.</p>
            </div>
        @endif
    </div>
</div>

@endsection


