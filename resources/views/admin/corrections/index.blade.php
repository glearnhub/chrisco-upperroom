@extends('layouts.admin')

@section('title', 'Correction Requests')
@section('page-title', 'Correction Requests')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Correction Requests</h1>
    @php $pending = $requests->where('status', 'pending')->count(); @endphp
    @if($pending)
        <span class="text-xs font-bold px-3 py-1 rounded-full text-white" style="background:#c0392b;">
            {{ $pending }} Pending
        </span>
    @endif
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    @if($requests->count())
        <div class="divide-y divide-gray-100">
            @foreach($requests as $req)
                @php
                    $statusColors = [
                        'pending'  => ['bg'=>'#fef3c7','text'=>'#92400e','label'=>'Pending'],
                        'reviewed' => ['bg'=>'#dbeafe','text'=>'#1e40af','label'=>'Reviewed'],
                        'resolved' => ['bg'=>'#d1fae5','text'=>'#065f46','label'=>'Resolved'],
                    ];
                    $sc = $statusColors[$req->status];
                @endphp
                <div class="p-5 {{ $req->status === 'pending' ? 'bg-yellow-50' : '' }}">
                    <div class="flex flex-col sm:flex-row items-start gap-4">
                        <div class="flex items-start gap-4 flex-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0 text-sm" style="background: #0a1f44;">
                                {{ strtoupper(substr($req->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="font-semibold text-gray-800 text-sm">
                                        {{ $req->user->name }} {{ $req->user->last_name ?? '' }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $req->user->email }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold"
                                        style="background: {{ $sc['bg'] }}; color: {{ $sc['text'] }};">
                                        {{ $sc['label'] }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ $req->message }}</p>
                                @if($req->admin_notes)
                                    <p class="text-xs text-blue-600 mt-2 italic">
                                        <i class="fas fa-reply mr-1"></i>Note: {{ $req->admin_notes }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-400 mt-2">
                                    <i class="fas fa-clock mr-1"></i>{{ $req->created_at->format('d M Y, g:i A') }}
                                </p>
                            </div>
                        </div>

                        {{-- Quick update form --}}
                        <form method="POST" action="{{ route('admin.corrections.update', $req) }}" class="flex-shrink-0 w-full sm:w-52">
                            @csrf @method('PATCH')
                            <select name="status" class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs mb-2">
                                <option value="pending"  {{ $req->status === 'pending'  ? 'selected' : '' }}>Pending</option>
                                <option value="reviewed" {{ $req->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                <option value="resolved" {{ $req->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                            <textarea name="admin_notes" rows="2" placeholder="Add a note (optional)..."
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs mb-2 resize-none">{{ $req->admin_notes }}</textarea>
                            <button type="submit" class="w-full py-1.5 rounded text-xs font-semibold text-white"
                                style="background: #0a1f44;">
                                Update
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="px-5 py-3 border-t">{{ $requests->links() }}</div>
    @else
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-check-double text-5xl mb-3 block"></i>
            <p class="font-semibold">No correction requests yet.</p>
        </div>
    @endif
</div>

@endsection
