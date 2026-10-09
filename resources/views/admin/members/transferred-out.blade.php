@extends('layouts.admin')
@section('title', 'Transferred Out Members')
@section('page-title', 'Transferred Out Members')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold" style="color:#0a1f44;">Transferred Out</h1>
        <p class="text-sm text-gray-500 mt-0.5">Members recorded as transferred to another Chrisco church</p>
    </div>
    <a href="{{ route('admin.members.index') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700">
        <i class="fas fa-arrow-left"></i> Back to Members
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border-l-4 overflow-hidden" style="border-color:#dc2626;">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
        <h2 class="font-bold" style="color:#dc2626;">
            <i class="fas fa-sign-out-alt mr-2"></i>Transferred Out Members
        </h2>
        <span class="px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-700">
            {{ $members->count() }} {{ $members->count() === 1 ? 'member' : 'members' }}
        </span>
    </div>

    @if($members->count())
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead style="background:#0a1f44;">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wide">#</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wide">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wide hidden sm:table-cell">Phone</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wide hidden sm:table-cell">Department</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wide">Transferred To</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($members as $i => $m)
            <tr class="hover:bg-red-50">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.members.show', $m) }}"
                       class="font-semibold hover:underline" style="color:#0a1f44;">
                        {{ $m->full_name }}
                    </a>
                    @if($m->office)
                    <div class="text-xs text-gray-400 mt-0.5">{{ $m->office }}</div>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $m->phone ?: '—' }}</td>
                <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $m->department ?: '—' }}</td>
                <td class="px-4 py-3">
                    @if($m->transfer_church)
                    <span class="inline-flex items-center gap-1 text-sm font-semibold text-blue-700">
                        <i class="fas fa-church text-xs"></i> {{ $m->transfer_church }}
                    </span>
                    @else
                    <span class="text-gray-400 text-xs italic">Not specified</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @else
    <div class="px-5 py-12 text-center text-gray-400">
        <i class="fas fa-sign-out-alt text-4xl mb-3 block text-gray-300"></i>
        <p class="font-medium text-gray-500">No transferred-out members recorded yet.</p>
        <p class="text-sm mt-1">When a follow-up reason of "Transferred to another Chrisco church" is recorded, the member will appear here.</p>
    </div>
    @endif
</div>

@endsection
