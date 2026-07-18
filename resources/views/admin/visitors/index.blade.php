@extends('layouts.admin')

@section('title', 'Visitors')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold" style="color:#0a1f44;">Visitors</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track and follow up on church visitors</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.visitors.import') }}"
               class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                <i class="fas fa-file-upload"></i> Import Excel
            </a>
            <a href="{{ route('admin.visitors.index', array_merge(request()->query(), ['export'=>'excel'])) }}"
               class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                <i class="fas fa-file-excel text-green-600"></i> Export Excel
            </a>
            <a href="{{ route('admin.visitors.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm rounded text-white font-semibold"
               style="background:#0a1f44;">
                <i class="fas fa-user-plus"></i> Add Visitor
            </a>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex items-center justify-between">
        <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded flex items-center justify-between">
        <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        @php
        $statCards = [
            ['label'=>'Total Visitors',     'value'=>$stats['total'],           'icon'=>'fa-users',          'color'=>'#0a1f44'],
            ['label'=>"Today's Visitors",   'value'=>$stats['today'],           'icon'=>'fa-calendar-day',   'color'=>'#2563eb'],
            ['label'=>'This Month',         'value'=>$stats['this_month'],      'icon'=>'fa-calendar-alt',   'color'=>'#7c3aed'],
            ['label'=>'Pending Follow-ups', 'value'=>$stats['pending_followup'],'icon'=>'fa-clock',          'color'=>'#c0392b'],
            ['label'=>'Returning Visitors', 'value'=>$stats['returning'],       'icon'=>'fa-redo',           'color'=>'#059669'],
        ];
        @endphp
        @foreach($statCards as $card)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background:{{ $card['color'] }}22;">
                <i class="fas {{ $card['icon'] }} text-sm" style="color:{{ $card['color'] }};"></i>
            </div>
            <div>
                <p class="text-2xl font-bold" style="color:{{ $card['color'] }};">{{ $card['value'] }}</p>
                <p class="text-xs text-gray-500 leading-tight">{{ $card['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search name, phone, email…"
                   class="border border-gray-300 rounded px-3 py-2 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-blue-300">

            <select name="follow_up" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                <option value="">All Follow-up Status</option>
                <option value="pending"   {{ request('follow_up')=='pending'   ? 'selected' : '' }}>Pending</option>
                <option value="contacted" {{ request('follow_up')=='contacted' ? 'selected' : '' }}>Contacted</option>
                <option value="completed" {{ request('follow_up')=='completed' ? 'selected' : '' }}>Completed</option>
            </select>

            <select name="how_heard" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                <option value="">All Sources</option>
                @foreach(['Friend','Family','Social Media','Website','Walk In','Evangelism','Other'] as $src)
                <option value="{{ $src }}" {{ request('how_heard')==$src ? 'selected' : '' }}>{{ $src }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 text-sm rounded text-white font-semibold" style="background:#0a1f44;">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search','follow_up','how_heard']))
            <a href="{{ route('admin.visitors.index') }}" class="px-3 py-2 text-sm rounded border border-gray-300 text-gray-600 hover:bg-gray-50">
                Clear
            </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($visitors->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-users text-4xl mb-3 block"></i>
            <p class="font-medium">No visitor records found</p>
            <p class="text-sm mt-1">
                @if(request()->hasAny(['search','follow_up','how_heard']))
                    Try adjusting your filters.
                @else
                    <a href="{{ route('admin.visitors.create') }}" class="text-blue-600 hover:underline">Add the first visitor</a>
                @endif
            </p>
        </div>
        @else
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-500 border-b border-gray-100">
                    <th class="px-4 py-3 font-semibold">#</th>
                    <th class="px-4 py-3 font-semibold">Name</th>
                    <th class="px-4 py-3 font-semibold">Phone</th>
                    <th class="px-4 py-3 font-semibold hidden md:table-cell">Visit Date</th>
                    <th class="px-4 py-3 font-semibold hidden lg:table-cell">How Heard</th>
                    <th class="px-4 py-3 font-semibold hidden lg:table-cell">Returning</th>
                    <th class="px-4 py-3 font-semibold">Follow-up</th>
                    <th class="px-4 py-3 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($visitors as $v)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-400">{{ $visitors->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $v->full_name }}</div>
                        @if($v->email)
                        <div class="text-xs text-gray-400">{{ $v->email }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $v->phone }}</td>
                    <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $v->visit_date?->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-gray-600 hidden lg:table-cell">{{ $v->how_heard ?? '—' }}</td>
                    <td class="px-4 py-3 hidden lg:table-cell">
                        @if($v->visited_before)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                <i class="fas fa-redo" style="font-size:0.6rem;"></i> Yes
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">First visit</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $badge = match($v->follow_up_status) {
                                'pending'   => 'bg-yellow-100 text-yellow-700',
                                'contacted' => 'bg-blue-100 text-blue-700',
                                'completed' => 'bg-green-100 text-green-700',
                                default     => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                            {{ ucfirst($v->follow_up_status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.visitors.show', $v) }}"
                               class="text-blue-600 hover:text-blue-800" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.visitors.edit', $v) }}"
                               class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.visitors.destroy', $v) }}"
                                  onsubmit="return confirm('Delete this visitor record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $visitors->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
