@extends('layouts.admin')

@section('title', 'About Us Management')
@section('page-title', 'About Us')

@section('content')

{{-- ── TABS ─────────────────────────────────────────────────────────── --}}
<div class="flex space-x-2 mb-6 border-b border-gray-200">
    <button onclick="showTab('info')" id="tab-info"
        class="tab-btn px-5 py-2 text-sm font-semibold border-b-2 border-transparent focus:outline-none">
        <i class="fas fa-info-circle mr-1"></i> Church Info
    </button>
    <button onclick="showTab('leaders')" id="tab-leaders"
        class="tab-btn px-5 py-2 text-sm font-semibold border-b-2 border-transparent focus:outline-none">
        <i class="fas fa-users mr-1"></i> Leadership ({{ $leaders->count() }})
    </button>
    <button onclick="showTab('pillars')" id="tab-pillars"
        class="tab-btn px-5 py-2 text-sm font-semibold border-b-2 border-transparent focus:outline-none">
        <i class="fas fa-columns mr-1"></i> Core Pillars ({{ $pillars->count() }})
    </button>
</div>

{{-- ── TAB: CHURCH INFO ─────────────────────────────────────────────── --}}
<div id="pane-info" class="tab-pane">
    <div class="max-w-3xl bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold mb-5" style="color: #0a1f44;">Church Information</h2>
        <form method="POST" action="{{ route('admin.about.updateInfo') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Who We Are</label>
                <textarea name="who_we_are" rows="6"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Describe your church — history, community, values...">{{ old('who_we_are', \App\Models\ChurchSetting::get('who_we_are')) }}</textarea>
                @error('who_we_are')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Vision Statement</label>
                <textarea name="vision" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Our vision is...">{{ old('vision', \App\Models\ChurchSetting::get('vision')) }}</textarea>
                @error('vision')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Mission Statement</label>
                <textarea name="mission" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Our mission is...">{{ old('mission', \App\Models\ChurchSetting::get('mission')) }}</textarea>
                @error('mission')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="btn-red px-6 py-2 font-semibold">
                <i class="fas fa-save mr-2"></i>Save Information
            </button>
        </form>
    </div>
</div>

{{-- ── TAB: LEADERSHIP ──────────────────────────────────────────────── --}}
<div id="pane-leaders" class="tab-pane hidden">

    {{-- Add Leader Form --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-bold mb-4" style="color: #0a1f44;"><i class="fas fa-user-plus mr-2"></i>Add Leader</h2>
        <form method="POST" action="{{ route('admin.about.leaders.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ps. John Doe">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Title / Position <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Senior Pastor">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role Category <span class="text-red-500">*</span></label>
                    <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="founder" {{ old('role') === 'founder' ? 'selected' : '' }}>Founder</option>
                        <option value="bishop"  {{ old('role') === 'bishop'  ? 'selected' : '' }}>Residing Pastor / Bishop</option>
                        <option value="pastor"  {{ old('role','pastor') === 'pastor'  ? 'selected' : '' }}>Pastor</option>
                        <option value="other"   {{ old('role') === 'other'   ? 'selected' : '' }}>Other Church Leader</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Photo</label>
                    <input type="file" name="photo" accept="image/*"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                    <p class="text-xs text-gray-400 mt-1">Max 2MB. JPG or PNG recommended.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bio / Short Description</label>
                    <textarea name="bio" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="A short biography...">{{ old('bio') }}</textarea>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn-red px-5 py-2 text-sm">
                    <i class="fas fa-plus mr-1"></i>Add Leader
                </button>
            </div>
        </form>
    </div>

    {{-- Leaders List --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead style="background: #0a1f44;">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Photo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Role</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Order</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($leaders as $leader)
                    <tr class="hover:bg-gray-50" id="leader-row-{{ $leader->id }}">
                        <td class="px-4 py-3">
                            @if($leader->photo)
                                <img src="{{ asset('storage/'.$leader->photo) }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background:#0a1f44;">
                                    {{ strtoupper(substr($leader->name,0,1)) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $leader->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $leader->title }}</td>
                        <td class="px-4 py-3">
                            @php $roleColors = ['founder'=>'bg-yellow-100 text-yellow-700','bishop'=>'bg-purple-100 text-purple-700','pastor'=>'bg-blue-100 text-blue-700','other'=>'bg-gray-100 text-gray-600']; @endphp
                            <span class="text-xs px-2 py-1 rounded-full {{ $roleColors[$leader->role] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $leader->role_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $leader->sort_order }}</td>
                        <td class="px-4 py-3 flex items-center space-x-2">
                            <button onclick="toggleEdit('leader-edit-{{ $leader->id }}')"
                                class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 rounded text-xs">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form method="POST" action="{{ route('admin.about.leaders.destroy', $leader) }}"
                                onsubmit="return confirm('Remove {{ $leader->name }}?')">
                                @csrf @method('DELETE')
                                <button class="bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded text-xs">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    {{-- Inline Edit Row --}}
                    <tr id="leader-edit-{{ $leader->id }}" class="hidden bg-blue-50">
                        <td colspan="6" class="px-4 py-4">
                            <form method="POST" action="{{ route('admin.about.leaders.update', $leader) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Name</label>
                                        <input type="text" name="name" value="{{ $leader->name }}"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Title</label>
                                        <input type="text" name="title" value="{{ $leader->title }}"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Role</label>
                                        <select name="role" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                                            <option value="founder" {{ $leader->role === 'founder' ? 'selected' : '' }}>Founder</option>
                                            <option value="bishop"  {{ $leader->role === 'bishop'  ? 'selected' : '' }}>Residing Pastor / Bishop</option>
                                            <option value="pastor"  {{ $leader->role === 'pastor'  ? 'selected' : '' }}>Pastor</option>
                                            <option value="other"   {{ $leader->role === 'other'   ? 'selected' : '' }}>Other Church Leader</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">New Photo</label>
                                        <input type="file" name="photo" accept="image/*"
                                            class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Display Order</label>
                                        <input type="number" name="sort_order" value="{{ $leader->sort_order }}" min="0"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                                    </div>
                                    <div class="flex items-end">
                                        <label class="flex items-center space-x-2 text-sm text-gray-700">
                                            <input type="checkbox" name="is_active" value="1" {{ $leader->is_active ? 'checked' : '' }}>
                                            <span>Active</span>
                                        </label>
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Bio</label>
                                        <textarea name="bio" rows="2"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">{{ $leader->bio }}</textarea>
                                    </div>
                                </div>
                                <div class="mt-3 flex space-x-2">
                                    <button type="submit" class="btn-red px-4 py-1.5 text-xs">Save Changes</button>
                                    <button type="button" onclick="toggleEdit('leader-edit-{{ $leader->id }}')"
                                        class="btn-navy px-4 py-1.5 text-xs">Cancel</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-10 text-gray-400">No leaders added yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── TAB: CORE PILLARS ────────────────────────────────────────────── --}}
<div id="pane-pillars" class="tab-pane hidden">

    {{-- Add Pillar Form --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-bold mb-4" style="color: #0a1f44;"><i class="fas fa-plus-circle mr-2"></i>Add Core Pillar</h2>
        <form method="POST" action="{{ route('admin.about.pillars.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pillar Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. Prayer, Worship, Evangelism">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Icon (Font Awesome class)</label>
                    <input type="text" name="icon" value="{{ old('icon', 'fas fa-cross') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="fas fa-praying-hands">
                    <p class="text-xs text-gray-400 mt-1">Visit <span class="font-mono">fontawesome.com/icons</span> for icon names.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Brief description of this pillar...">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn-red px-5 py-2 text-sm">
                    <i class="fas fa-plus mr-1"></i>Add Pillar
                </button>
            </div>
        </form>
    </div>

    {{-- Pillars List --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead style="background: #0a1f44;">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Icon</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Order</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Active</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pillars as $pillar)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background: #0a1f44;">
                                <i class="{{ $pillar->icon }} text-yellow-400 text-sm"></i>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $pillar->title }}</td>
                        <td class="px-4 py-3 text-gray-500 max-w-xs">{{ Str::limit($pillar->description, 70) }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $pillar->sort_order }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded-full {{ $pillar->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $pillar->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 flex items-center space-x-2">
                            <button onclick="toggleEdit('pillar-edit-{{ $pillar->id }}')"
                                class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 rounded text-xs">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form method="POST" action="{{ route('admin.about.pillars.destroy', $pillar) }}"
                                onsubmit="return confirm('Remove this pillar?')">
                                @csrf @method('DELETE')
                                <button class="bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded text-xs">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <tr id="pillar-edit-{{ $pillar->id }}" class="hidden bg-blue-50">
                        <td colspan="6" class="px-4 py-4">
                            <form method="POST" action="{{ route('admin.about.pillars.update', $pillar) }}">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Title</label>
                                        <input type="text" name="title" value="{{ $pillar->title }}"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Icon class</label>
                                        <input type="text" name="icon" value="{{ $pillar->icon }}"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Order</label>
                                        <input type="number" name="sort_order" value="{{ $pillar->sort_order }}" min="0"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                                        <textarea name="description" rows="2"
                                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">{{ $pillar->description }}</textarea>
                                    </div>
                                    <div class="flex items-end">
                                        <label class="flex items-center space-x-2 text-sm text-gray-700">
                                            <input type="checkbox" name="is_active" value="1" {{ $pillar->is_active ? 'checked' : '' }}>
                                            <span>Active / Visible</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-3 flex space-x-2">
                                    <button type="submit" class="btn-red px-4 py-1.5 text-xs">Save</button>
                                    <button type="button" onclick="toggleEdit('pillar-edit-{{ $pillar->id }}')"
                                        class="btn-navy px-4 py-1.5 text-xs">Cancel</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-10 text-gray-400">No pillars added yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showTab(name) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('border-red-600', 'text-red-700');
        b.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById('pane-' + name).classList.remove('hidden');
    const btn = document.getElementById('tab-' + name);
    btn.classList.remove('border-transparent', 'text-gray-500');
    btn.classList.add('border-red-600', 'text-red-700');
}

function toggleEdit(id) {
    const row = document.getElementById(id);
    row.classList.toggle('hidden');
}

// Auto-open tab based on hash or default
document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash.replace('#','') || 'info';
    const valid = ['info','leaders','pillars'];
    showTab(valid.includes(hash) ? hash : 'info');
});
</script>
@endpush


