@extends('layouts.admin')
@section('title', 'Edit Calendar Event')

@section('content')
<div class="p-3 sm:p-6 max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.calendar.index', ['year' => $calendar->calendar_year]) }}" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold" style="color:#1e3a6e;">Edit Calendar Event</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.calendar.update', $calendar) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Event Title *</label>
                    <input type="text" name="title" value="{{ old('title', $calendar->title) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $calendar->start_date->toDateString()) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">End Date <span class="text-gray-400 font-normal normal-case">(leave blank for single day)</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', $calendar->end_date?->toDateString()) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Category *</label>
                    <select name="category" id="cat-select" onchange="updateColor()" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">
                        @foreach($categories as $key => $cat)
                        <option value="{{ $key }}" data-color="{{ $cat['color'] }}"
                                {{ old('category', $calendar->category) == $key ? 'selected' : '' }}>
                            {{ $cat['label'] }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Display Colour</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" id="color-picker" value="{{ old('color', $calendar->color) }}"
                               class="h-10 w-16 rounded border border-gray-300 cursor-pointer">
                        <div class="flex gap-2 flex-wrap">
                            <button type="button" onclick="setColor('#1e3a6e')" class="w-6 h-6 rounded-full border-2 border-white shadow" style="background:#1e3a6e;"></button>
                            <button type="button" onclick="setColor('#c0392b')" class="w-6 h-6 rounded-full border-2 border-white shadow" style="background:#c0392b;"></button>
                            <button type="button" onclick="setColor('#0e7490')" class="w-6 h-6 rounded-full border-2 border-white shadow" style="background:#0e7490;"></button>
                            <button type="button" onclick="setColor('#16a34a')" class="w-6 h-6 rounded-full border-2 border-white shadow" style="background:#16a34a;"></button>
                            <button type="button" onclick="setColor('#7c3aed')" class="w-6 h-6 rounded-full border-2 border-white shadow" style="background:#7c3aed;"></button>
                            <button type="button" onclick="setColor('#d97706')" class="w-6 h-6 rounded-full border-2 border-white shadow" style="background:#d97706;"></button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Calendar Year *</label>
                    <select name="calendar_year" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ old('calendar_year', $calendar->calendar_year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Visibility</label>
                    <div class="flex gap-2">
                        <label class="flex items-center gap-2 px-4 py-2 rounded-lg border-2 cursor-pointer transition"
                               id="lbl-public"
                               style="{{ old('is_published', $calendar->is_published) ? 'border-color:#16a34a; background:#f0fdf4;' : 'border-color:#e5e7eb; background:#fff;' }}">
                            <input type="radio" name="is_published" value="1" class="hidden" id="vis-public"
                                   {{ old('is_published', $calendar->is_published) ? 'checked' : '' }}
                                   onchange="updateVisLabels()">
                            <i class="fas fa-globe text-green-600"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Public</p>
                                <p class="text-xs text-gray-500">Visible on the church website</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 px-4 py-2 rounded-lg border-2 cursor-pointer transition"
                               id="lbl-private"
                               style="{{ !old('is_published', $calendar->is_published) ? 'border-color:#6b7280; background:#f9fafb;' : 'border-color:#e5e7eb; background:#fff;' }}">
                            <input type="radio" name="is_published" value="0" class="hidden" id="vis-private"
                                   {{ !old('is_published', $calendar->is_published) ? 'checked' : '' }}
                                   onchange="updateVisLabels()">
                            <i class="fas fa-lock text-gray-500"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Private</p>
                                <p class="text-xs text-gray-500">Admin only, hidden from public</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">{{ old('notes', $calendar->notes) }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                        class="px-6 py-2 rounded-lg text-white text-sm font-semibold" style="background:#1e3a6e;">
                    <i class="fas fa-save mr-1"></i> Update Event
                </button>
                <a href="{{ route('admin.calendar.index', ['year' => $calendar->calendar_year]) }}"
                   class="px-6 py-2 rounded-lg border border-gray-300 text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
                <form method="POST" action="{{ route('admin.calendar.destroy', $calendar) }}"
                      data-confirm="Delete this event?" data-confirm-ok="Delete" class="ml-auto">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-100 text-red-700 text-sm font-semibold hover:bg-red-200">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>

<script>
function updateVisLabels() {
    const pub = document.getElementById('vis-public').checked;
    document.getElementById('lbl-public').style.cssText  = pub ? 'border-color:#16a34a;background:#f0fdf4;' : 'border-color:#e5e7eb;background:#fff;';
    document.getElementById('lbl-private').style.cssText = !pub ? 'border-color:#6b7280;background:#f9fafb;' : 'border-color:#e5e7eb;background:#fff;';
}
const catColors = @json(collect($categories)->mapWithKeys(fn($c,$k) => [$k => $c['color']]));
function updateColor() {
    const col = catColors[document.getElementById('cat-select').value] || '#1e3a6e';
    document.getElementById('color-picker').value = col;
}
function setColor(hex) { document.getElementById('color-picker').value = hex; }
</script>
@endsection
