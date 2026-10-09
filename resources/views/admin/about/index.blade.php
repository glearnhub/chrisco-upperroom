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
    <button onclick="showTab('service-times')" id="tab-service-times"
        class="tab-btn px-5 py-2 text-sm font-semibold border-b-2 border-transparent focus:outline-none">
        <i class="fas fa-clock mr-1"></i> Service Times
    </button>
</div>

{{-- ── TAB: CHURCH INFO ─────────────────────────────────────────────── --}}
<div id="pane-info" class="tab-pane">

    {{-- Hero Image Upload --}}
    <div class="max-w-3xl bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-bold mb-1" style="color: #0a1f44;"><i class="fas fa-image mr-2"></i>Homepage Hero Background</h2>
        <p class="text-sm text-gray-500 mb-5">This image appears as the background of the welcome banner on the homepage. Recommended: landscape photo, at least 1400×800px, JPG/PNG/WebP, max 5MB.</p>

        @php $heroImage = \App\Models\ChurchSetting::get('hero_image'); @endphp

        {{-- Current preview --}}
        @if($heroImage)
        <div class="mb-4 relative rounded-xl overflow-hidden" style="height: 180px;">
            <img src="{{ asset('storage/' . $heroImage) }}" alt="Current hero image"
                class="w-full h-full object-cover">
            <div class="absolute inset-0 flex items-center justify-center"
                style="background: rgba(10,31,68,0.45);">
                <span class="text-white text-sm font-semibold"><i class="fas fa-check-circle mr-1 text-green-400"></i>Current hero image</span>
            </div>
        </div>
        @else
        <div class="mb-4 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center" style="height: 140px; background: #f8fafc;">
            <span class="text-gray-400 text-sm"><i class="fas fa-image mr-2"></i>No hero image set — using default gradient</span>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.about.hero-image.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="flex items-center gap-4">
                <input type="file" name="hero_image" accept="image/*" id="hero-img-input"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:cursor-pointer"
                    style="file-selector-button-background: #0a1f44; file-selector-button-color: white;">
                <button type="submit" class="btn-red px-5 py-2 text-sm whitespace-nowrap">
                    <i class="fas fa-upload mr-1"></i> Upload Image
                </button>
            </div>
            @error('hero_image')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
        </form>

        {{-- Live preview before upload --}}
        <div id="hero-preview-wrap" class="hidden mt-4 rounded-xl overflow-hidden" style="height: 160px;">
            <img id="hero-preview-img" src="" alt="Preview" class="w-full h-full object-cover">
        </div>
    </div>

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
    @php
        $memberJson = $members->map(fn($m) => [
            'id'     => $m->id,
            'label'  => trim($m->name . ' ' . $m->middle_name . ' ' . $m->last_name),
            'office' => $m->office,
            'photo'  => $m->profile_photo ? asset('storage/'.$m->profile_photo) : null,
        ])->values();

        $officeTitle = ['presbyter'=>'Presb.','pastor'=>'Ps.','elder'=>'Elder','deacon'=>'Deacon','deaconess'=>'Deaconess'];
    @endphp
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-bold mb-1" style="color: #0a1f44;"><i class="fas fa-user-plus mr-2"></i>Add Leader</h2>
        <p class="text-xs text-gray-400 mb-5">Select one member, or two for a couple. Their names auto-fill — just set the ministry role and upload a shared photo.</p>

        <form method="POST" action="{{ route('admin.about.leaders.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Person 1 --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Person 1 <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="text" id="search-1" autocomplete="off" placeholder="Type to search members…"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div id="dropdown-1" class="absolute z-30 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-52 overflow-y-auto hidden"></div>
                </div>
            </div>

            {{-- Add Couple toggle --}}
            <div class="mb-4">
                <button type="button" id="add-couple-btn"
                        onclick="toggleCouple()"
                        class="text-xs text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1">
                    <i class="fas fa-user-plus"></i> Add Spouse / Partner (couple)
                </button>
            </div>

            {{-- Person 2 (hidden by default) --}}
            <div id="person2-section" class="hidden mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Person 2 <span class="text-gray-400 font-normal">(Spouse / Partner)</span>
                    <button type="button" onclick="toggleCouple()" class="ml-2 text-xs text-red-400 hover:text-red-600">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </label>
                <div class="relative">
                    <input type="text" id="search-2" autocomplete="off" placeholder="Type to search members…"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div id="dropdown-2" class="absolute z-30 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-52 overflow-y-auto hidden"></div>
                </div>
            </div>

            {{-- Auto-generated name preview --}}
            <div id="name-preview" class="hidden mb-5 flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
                <i class="fas fa-user-friends text-blue-400 text-lg"></i>
                <p id="name-preview-text" class="text-sm font-semibold text-gray-800"></p>
                <button type="button" onclick="clearAll()" class="ml-auto text-gray-400 hover:text-red-500 text-xs"><i class="fas fa-times"></i> Clear</button>
            </div>

            {{-- Hidden name field --}}
            <input type="hidden" name="name" id="field-name" value="{{ old('name') }}" required>

            @error('name')<p class="text-red-500 text-xs mb-3">{{ $message }}</p>@enderror

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Display Name / Title <span class="text-red-500">*</span>
                        <span class="ml-1 text-gray-400 font-normal text-xs">— auto-filled, edit freely</span>
                    </label>
                    <input type="text" name="title" id="field-title" value="{{ old('title') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g. Elder Daniel Njenga and Deaconess Christine Njenga">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ministry Role <span class="text-red-500">*</span></label>
                    <input type="text" name="role" id="field-role" value="{{ old('role') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g. Worship Team Leaders, Evangelism Coordinators, Senior Pastor…">
                    @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Photo <span class="text-gray-400 font-normal text-xs">(shared for couple)</span></label>
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
                              placeholder="A short biography…">{{ old('bio') }}</textarea>
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
                                data-confirm="Remove {{ $leader->name }}?" data-confirm-ok="Remove">
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
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Ministry Role</label>
                                        <input type="text" name="role" value="{{ $leader->role }}"
                                               class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none"
                                               placeholder="e.g. Worship Team Leaders">
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
                                data-confirm="Remove this pillar?" data-confirm-ok="Remove">
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

{{-- ── TAB: SERVICE TIMES ───────────────────────────────────────────── --}}
<div id="pane-service-times" class="tab-pane hidden">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold mb-1" style="color: #0a1f44;"><i class="fas fa-clock mr-2"></i>Service Times</h2>
        <p class="text-sm text-gray-500 mb-6">These are displayed on the homepage. Edit each service's name, time, subtitle and icon.</p>

        @if(session('success') && request()->is('*/about*'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.about.service-times.update') }}">
            @csrf
            @php
                $iconOptions = [
                    'fas fa-sun'            => 'Sun',
                    'fas fa-book-open'      => 'Book Open',
                    'fas fa-praying-hands'  => 'Praying Hands',
                    'fas fa-church'         => 'Church',
                    'fas fa-cross'          => 'Cross',
                    'fas fa-dove'           => 'Dove',
                    'fas fa-hands'          => 'Hands',
                    'fas fa-star'           => 'Star',
                    'fas fa-fire'           => 'Fire',
                    'fas fa-music'          => 'Music',
                    'fas fa-bible'          => 'Bible',
                    'fas fa-moon'           => 'Moon',
                ];
                $colorOptions = [
                    '#c0392b' => 'Red',
                    '#0a1f44' => 'Navy',
                    '#f0a500' => 'Gold',
                    '#27ae60' => 'Green',
                    '#8e44ad' => 'Purple',
                    '#2980b9' => 'Blue',
                ];
            @endphp

            @foreach([1,2,3] as $i)
            @php $s = $serviceTimes[$i]; @endphp
            <div class="border border-gray-200 rounded-xl p-5 mb-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                         style="background: {{ $s['color'] ?: '#0a1f44' }};">
                        <i class="{{ $s['icon'] ?: 'fas fa-church' }} text-white text-sm"></i>
                    </div>
                    <h3 class="font-bold text-gray-700">Service {{ $i }}</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Service Name <span class="text-red-500">*</span></label>
                        <input type="text" name="service[{{ $i }}][name]" value="{{ old("service.{$i}.name", $s['name']) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="e.g. Sunday Service">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Subtitle</label>
                        <input type="text" name="service[{{ $i }}][subtitle]" value="{{ old("service.{$i}.subtitle", $s['subtitle']) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="e.g. Main Worship Service">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Time <span class="text-red-500">*</span></label>
                        <input type="text" name="service[{{ $i }}][time]" value="{{ old("service.{$i}.time", $s['time']) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="e.g. 9:00 AM or 8:00 PM – 5:00 AM">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Border / Icon Colour</label>
                        <select name="service[{{ $i }}][color]"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                            @foreach($colorOptions as $hex => $label)
                                <option value="{{ $hex }}" {{ old("service.{$i}.color", $s['color']) === $hex ? 'selected' : '' }}>
                                    {{ $label }} ({{ $hex }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Icon</label>
                        <select name="service[{{ $i }}][icon]"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                            @foreach($iconOptions as $class => $label)
                                <option value="{{ $class }}" {{ old("service.{$i}.icon", $s['icon']) === $class ? 'selected' : '' }}>
                                    {{ $label }} ({{ $class }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Visit <span class="font-mono">fontawesome.com/icons</span> for more icon names.</p>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="mt-2">
                <button type="submit" class="btn-red px-6 py-2 text-sm">
                    <i class="fas fa-save mr-1"></i> Save Service Times
                </button>
            </div>
        </form>
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
    const valid = ['info','leaders','pillars','service-times'];
    showTab(valid.includes(hash) ? hash : 'info');

    // Hero image live preview
    const heroInput = document.getElementById('hero-img-input');
    if (heroInput) {
        heroInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const wrap = document.getElementById('hero-preview-wrap');
            const img  = document.getElementById('hero-preview-img');
            img.src = URL.createObjectURL(file);
            wrap.classList.remove('hidden');
        });
    }

    // ── Member search / auto-populate (couple-aware) ───────────────
    const members = @json($memberJson);

    const officePrefix = {
        presbyter: 'Presb.', pastor: 'Ps.', elder: 'Elder',
        deacon: 'Deacon', deaconess: 'Deaconess'
    };

    let selected = { 1: null, 2: null };
    let coupleVisible = false;

    window.toggleCouple = function() {
        coupleVisible = !coupleVisible;
        document.getElementById('person2-section').classList.toggle('hidden', !coupleVisible);
        document.getElementById('add-couple-btn').classList.toggle('hidden', coupleVisible);
        if (!coupleVisible) {
            selected[2] = null;
            document.getElementById('search-2').value = '';
            rebuildNameFields();
        }
    };

    function makeDropdown(slot) {
        const searchEl   = document.getElementById('search-' + slot);
        const dropdownEl = document.getElementById('dropdown-' + slot);

        function render(filtered) {
            dropdownEl.innerHTML = '';
            if (!filtered.length) {
                dropdownEl.innerHTML = '<div class="px-4 py-3 text-sm text-gray-400">No members found</div>';
            }
            filtered.forEach(m => {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-3 px-4 py-2.5 hover:bg-blue-50 cursor-pointer text-sm';
                const initials = m.label.split(' ').map(w => w[0] || '').slice(0, 2).join('').toUpperCase();
                div.innerHTML = m.photo
                    ? `<img src="${m.photo}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">`
                    : `<div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:#0a1f44;">${initials}</div>`;
                div.innerHTML += `<div><span class="font-medium text-gray-800">${m.label}</span> <span class="text-xs text-gray-400 capitalize ml-1">${m.office}</span></div>`;
                div.addEventListener('click', () => {
                    selected[slot] = m;
                    searchEl.value = m.label;
                    dropdownEl.classList.add('hidden');
                    rebuildNameFields();
                });
                dropdownEl.appendChild(div);
            });
            dropdownEl.classList.remove('hidden');
        }

        searchEl.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            if (!q) { dropdownEl.classList.add('hidden'); return; }
            render(members.filter(m => m.label.toLowerCase().includes(q) || m.office.toLowerCase().includes(q)));
        });
        searchEl.addEventListener('focus', function () {
            const q = this.value.toLowerCase().trim();
            if (q) render(members.filter(m => m.label.toLowerCase().includes(q)));
        });
        document.addEventListener('click', e => {
            if (!searchEl.contains(e.target) && !dropdownEl.contains(e.target))
                dropdownEl.classList.add('hidden');
        });
    }

    function personTitle(m) {
        const prefix = officePrefix[m.office] || '';
        return prefix ? prefix + ' ' + m.label : m.label;
    }

    function rebuildNameFields() {
        const p1 = selected[1], p2 = selected[2];
        const nameField  = document.getElementById('field-name');
        const titleField = document.getElementById('field-title');
        const preview    = document.getElementById('name-preview');
        const previewTxt = document.getElementById('name-preview-text');

        if (!p1 && !p2) {
            nameField.value = '';
            preview.classList.add('hidden');
            return;
        }

        let combinedName, combinedTitle;
        if (p1 && p2) {
            combinedName  = p1.label + ' & ' + p2.label;
            combinedTitle = personTitle(p1) + ' & ' + personTitle(p2);
        } else {
            const p = p1 || p2;
            combinedName  = p.label;
            combinedTitle = personTitle(p);
        }

        nameField.value  = combinedName;
        titleField.value = combinedTitle;

        previewTxt.textContent = combinedTitle;
        preview.classList.remove('hidden');
    }

    window.clearAll = function () {
        selected = { 1: null, 2: null };
        document.getElementById('search-1').value = '';
        document.getElementById('search-2').value = '';
        document.getElementById('field-name').value  = '';
        document.getElementById('field-title').value = '';
        document.getElementById('name-preview').classList.add('hidden');
    };

    makeDropdown(1);
    makeDropdown(2);
});
</script>
@endpush


