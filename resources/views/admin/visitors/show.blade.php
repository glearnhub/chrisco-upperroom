@extends('layouts.admin')

@section('title', $visitor->full_name . ' — Visitor')

@section('content')
<div class="p-6 max-w-3xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.visitors.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold" style="color:#0a1f44;">{{ $visitor->full_name }}</h1>
            <p class="text-sm text-gray-500">Visitor — {{ $visitor->visit_date?->format('d M Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.visitors.edit', $visitor) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form method="POST" action="{{ route('admin.visitors.destroy', $visitor) }}"
                  onsubmit="return confirm('Delete this visitor record?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm rounded bg-red-50 border border-red-200 text-red-600 hover:bg-red-100">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex items-center justify-between">
        <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
    @endif

    {{-- Follow-up status badge + quick update --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4 flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-500 mb-1 uppercase tracking-wider">Follow-up Status</p>
            @php
            $badge = match($visitor->follow_up_status) {
                'pending'   => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                'contacted' => 'bg-blue-100 text-blue-700 border-blue-200',
                'completed' => 'bg-green-100 text-green-700 border-green-200',
                default     => 'bg-gray-100 text-gray-600',
            };
            @endphp
            <span class="px-3 py-1 rounded-full text-sm font-semibold border {{ $badge }}">
                {{ ucfirst($visitor->follow_up_status) }}
            </span>
        </div>
        <form method="POST" action="{{ route('admin.visitors.update', $visitor) }}" class="flex items-center gap-2">
            @csrf @method('PUT')
            {{-- Hidden fields to keep all data intact --}}
            <input type="hidden" name="full_name" value="{{ $visitor->full_name }}">
            <input type="hidden" name="phone" value="{{ $visitor->phone }}">
            <input type="hidden" name="visit_date" value="{{ $visitor->visit_date?->format('Y-m-d') }}">
            @foreach($visitor->preferred_contact ?? [] as $pc)
            <input type="hidden" name="preferred_contact[]" value="{{ $pc }}">
            @endforeach
            <input type="hidden" name="visited_before" value="{{ $visitor->visited_before ? 1 : 0 }}">
            <input type="hidden" name="is_chrisco_member" value="{{ $visitor->is_chrisco_member ? 1 : 0 }}">
            <input type="hidden" name="attends_another_church" value="{{ $visitor->attends_another_church ? 1 : 0 }}">
            {{-- rest of fields --}}
            <input type="hidden" name="gender" value="{{ $visitor->gender }}">
            <input type="hidden" name="residence" value="{{ $visitor->residence }}">
            <input type="hidden" name="occupation" value="{{ $visitor->occupation }}">
            <input type="hidden" name="marital_status" value="{{ $visitor->marital_status }}">
            <input type="hidden" name="email" value="{{ $visitor->email }}">
            <input type="hidden" name="chrisco_church" value="{{ $visitor->chrisco_church }}">
            <input type="hidden" name="another_church_name" value="{{ $visitor->another_church_name }}">
            <input type="hidden" name="invited_by" value="{{ $visitor->invited_by }}">
            <input type="hidden" name="how_heard" value="{{ $visitor->how_heard }}">
            <input type="hidden" name="prayer_request" value="{{ $visitor->prayer_request }}">
            <input type="hidden" name="notes" value="{{ $visitor->notes }}">

            <select name="follow_up_status" class="border border-gray-300 rounded px-2 py-1 text-sm">
                @foreach(['pending'=>'Pending','contacted'=>'Contacted','completed'=>'Completed'] as $k=>$lbl)
                <option value="{{ $k }}" {{ $visitor->follow_up_status == $k ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-3 py-1 text-sm rounded text-white font-semibold" style="background:#0a1f44;">
                Update
            </button>
        </form>
    </div>

    {{-- Details card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4">
        <h2 class="font-semibold text-gray-700 mb-4 text-sm uppercase tracking-wider">Personal Details</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            @php
            $fields = [
                'Gender'         => $visitor->gender,
                'Marital Status' => $visitor->marital_status,
                'Residence'      => $visitor->residence,
                'Occupation'     => $visitor->occupation,
                'Phone'          => $visitor->phone,
                'Email'          => $visitor->email,
                'Visit Date'     => $visitor->visit_date?->format('d M Y'),
                'Invited By'     => $visitor->invited_by,
                'How Heard'      => $visitor->how_heard,
            ];
            @endphp
            @foreach($fields as $label => $val)
            <div>
                <dt class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">{{ $label }}</dt>
                <dd class="text-gray-800 font-medium">{{ $val ?: '—' }}</dd>
            </div>
            @endforeach

            <div>
                <dt class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Preferred Contact</dt>
                <dd class="text-gray-800 font-medium">
                    {{ $visitor->preferred_contact ? implode(', ', array_map('ucfirst', $visitor->preferred_contact)) : '—' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Visited Before</dt>
                <dd>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $visitor->visited_before ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $visitor->visited_before ? 'Yes' : 'No' }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Chrisco Member</dt>
                <dd class="text-gray-800 font-medium">
                    {{ $visitor->is_chrisco_member ? 'Yes' . ($visitor->chrisco_church ? ' — ' . $visitor->chrisco_church : '') : 'No' }}
                </dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Attends Another Church</dt>
                <dd class="text-gray-800 font-medium">
                    {{ $visitor->attends_another_church ? 'Yes' . ($visitor->another_church_name ? ' — ' . $visitor->another_church_name : '') : 'No' }}
                </dd>
            </div>
        </dl>
    </div>

    @if($visitor->prayer_request)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4">
        <h2 class="font-semibold text-gray-700 mb-2 text-sm uppercase tracking-wider">Prayer Request</h2>
        <p class="text-sm text-gray-700 leading-relaxed">{{ $visitor->prayer_request }}</p>
    </div>
    @endif

    @if($visitor->notes)
    <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-5 mb-4">
        <h2 class="font-semibold text-yellow-800 mb-2 text-sm uppercase tracking-wider">
            <i class="fas fa-sticky-note mr-1"></i> Internal Notes
        </h2>
        <p class="text-sm text-yellow-900 leading-relaxed">{{ $visitor->notes }}</p>
    </div>
    @endif

    <p class="text-xs text-gray-400 text-right">
        Added {{ $visitor->created_at->diffForHumans() }}
        @if($visitor->updated_at->ne($visitor->created_at))
        &bull; Updated {{ $visitor->updated_at->diffForHumans() }}
        @endif
    </p>
</div>
@endsection
