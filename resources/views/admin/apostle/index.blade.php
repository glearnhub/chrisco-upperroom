@extends('layouts.admin')

@section('title', 'Apostle Das Teachings')
@section('page-title', 'Apostle Das Teachings')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color:#0a1f44;">Apostle Das Teachings</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.apostle.categories.index') }}" class="btn-navy px-4 py-2 text-sm">
            <i class="fas fa-tags mr-2"></i>Manage Categories
        </a>
        <a href="{{ route('admin.apostle.import') }}" class="px-4 py-2 text-sm font-semibold rounded text-white flex items-center" style="background:#1d6f42;">
            <i class="fas fa-file-excel mr-2"></i>Import Excel
        </a>
        <a href="{{ route('admin.apostle.create') }}" class="btn-red px-4 py-2 text-sm">
            <i class="fas fa-plus mr-2"></i>Add Teaching
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
@endif

@if(session('import_errors'))
    <div class="mb-4 bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded text-sm">
        <p class="font-semibold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i>Some rows were skipped:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead style="background:#0a1f44; color:white;">
            <tr>
                <th class="px-4 py-3 text-left">Title</th>
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Views</th>
                <th class="px-4 py-3 text-left">Added</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($teachings as $teaching)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium" style="color:#0a1f44;">{{ Str::limit($teaching->title, 50) }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $teaching->category?->name ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $teaching->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($teaching->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ number_format($teaching->views) }}</td>
                <td class="px-4 py-3 text-gray-400">{{ $teaching->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3 flex gap-2">
                    <a href="{{ route('admin.apostle.edit', $teaching) }}" class="btn-navy px-3 py-1 text-xs">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.apostle.destroy', $teaching) }}"
                        onsubmit="return confirm('Delete this teaching?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-red px-3 py-1 text-xs">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                    <i class="fas fa-video text-3xl mb-2 block"></i>No teachings yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($teachings->hasPages())
        <div class="p-4 border-t">{{ $teachings->links() }}</div>
    @endif
</div>

@endsection
