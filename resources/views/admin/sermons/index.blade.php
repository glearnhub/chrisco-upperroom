@extends('layouts.admin')

@section('title', 'Manage Sermons')
@section('page-title', 'Sermons')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Sermons</h1>
    <a href="{{ route('admin.sermons.create') }}" class="btn-red px-4 py-2 text-sm">
        <i class="fas fa-plus mr-2"></i>Add Sermon
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        @if($sermons->count())
            <table class="w-full text-sm">
                <thead style="background: #0a1f44;">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Speaker</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Views</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sermons as $sermon)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $sermon->title }}</p>
                                @if($sermon->series)
                                    <span class="text-xs text-red-600">{{ $sermon->series }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $sermon->speaker }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ \Carbon\Carbon::parse($sermon->sermon_date)->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full {{ $sermon->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ucfirst($sermon->status ?? 'draft') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $sermon->views ?? 0 }}</td>
                            <td class="px-4 py-3 flex items-center space-x-2">
                                <a href="{{ route('admin.sermons.edit', $sermon) }}"
                                    class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('admin.sermons.destroy', $sermon) }}"
                                    onsubmit="return confirm('Delete this sermon?')">
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
            <div class="px-4 py-3">{{ $sermons->links() }}</div>
        @else
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-bible text-5xl mb-3"></i>
                <p>No sermons yet. <a href="{{ route('admin.sermons.create') }}" class="text-blue-600 underline">Add one now</a>.</p>
            </div>
        @endif
    </div>
</div>

@endsection


