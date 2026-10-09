@extends('layouts.admin')

@section('title', 'Teachings')
@section('page-title', 'Teachings')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Text Teachings</h1>
    <a href="{{ route('admin.teachings.create') }}" class="btn-red px-4 py-2 text-sm">
        <i class="fas fa-plus mr-2"></i>Add Teaching
    </a>
</div>

@if($teachings->count())
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Title</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Pastor</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Category</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Date</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($teachings as $teaching)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center space-x-3">
                        @if($teaching->image)
                            <img src="{{ Storage::url($teaching->image) }}" class="w-10 h-10 rounded object-cover flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded flex items-center justify-center flex-shrink-0" style="background: #0a1f44;">
                                <i class="fas fa-book-open text-yellow-400 text-xs"></i>
                            </div>
                        @endif
                        <span class="font-medium text-gray-800">{{ Str::limit($teaching->title, 50) }}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $teaching->pastor_name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $teaching->category ?: '—' }}</td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $teaching->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $teaching->is_published ? 'Published' : 'Draft' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $teaching->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('teachings.show', $teaching) }}" target="_blank"
                           class="text-blue-500 hover:text-blue-700 text-xs" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.teachings.edit', $teaching) }}"
                           class="text-yellow-600 hover:text-yellow-800 text-xs" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.teachings.destroy', $teaching) }}"
                              data-confirm="Delete this teaching?" data-confirm-ok="Delete">
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
    <div class="px-4 py-3">{{ $teachings->links() }}</div>
</div>
@else
    <div class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
        <i class="fas fa-book-open text-4xl mb-3"></i>
        <p>No teachings yet. <a href="{{ route('admin.teachings.create') }}" class="text-red-600 underline">Add one.</a></p>
    </div>
@endif

@endsection


