@extends('layouts.admin')
@section('title', 'Publish as Event — ' . $calendar->title)

@section('content')
<div class="p-3 sm:p-6 max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.calendar.index', ['year' => $calendar->calendar_year, 'month' => $calendar->start_date->month]) }}"
           class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold" style="color:#1e3a6e;">Publish as Event</h1>
            <p class="text-sm text-gray-500 mt-0.5">This will create a public event card for the website, linked to the calendar entry.</p>
        </div>
    </div>

    {{-- Calendar event summary --}}
    <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 mb-6">
        <span class="inline-block w-3 h-3 rounded-full flex-shrink-0" style="background:{{ $calendar->color }};"></span>
        <div>
            <p class="font-semibold text-gray-800 text-sm">{{ $calendar->title }}</p>
            <p class="text-xs text-gray-500 mt-0.5">
                <i class="fas fa-calendar mr-1"></i>
                {{ $calendar->start_date->format('d F Y') }}
                @if($calendar->end_date && !$calendar->end_date->eq($calendar->start_date))
                    &ndash; {{ $calendar->end_date->format('d F Y') }}
                @endif
                &nbsp;·&nbsp;
                <span class="capitalize">{{ $calendar->category }}</span>
            </p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.calendar.publish-event.store', $calendar) }}"
              enctype="multipart/form-data">
            @csrf

            {{-- Poster upload --}}
            <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">
                    Event Poster / Image <span class="text-red-500">*</span>
                </label>

                <div id="drop-zone"
                     class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-blue-400 transition"
                     onclick="document.getElementById('poster-input').click()">
                    <img id="preview-img" src="" alt="" class="hidden max-h-56 mx-auto rounded-lg mb-3 object-contain">
                    <div id="drop-placeholder">
                        <i class="fas fa-image text-3xl text-gray-300 mb-2 block"></i>
                        <p class="text-sm text-gray-500">Click or drag &amp; drop a poster image here</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — max 5 MB</p>
                    </div>
                    <input type="file" id="poster-input" name="poster" accept="image/*" class="hidden"
                           onchange="previewPoster(this)">
                </div>
                @error('poster')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Time fields --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Start Time</label>
                    <input type="time" name="start_time" value="{{ old('start_time', '08:00') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">End Time</label>
                    <input type="time" name="end_time" value="{{ old('end_time', '17:00') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">
                </div>
            </div>

            {{-- Location --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Location <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
                <input type="text" name="location" value="{{ old('location') }}"
                       placeholder="e.g. Main Sanctuary, Nairobi"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">
            </div>

            {{-- Registration --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Registration</label>
                <div class="flex gap-3">
                    <label id="lbl-reg-no"
                           class="flex items-center gap-2 px-4 py-2.5 rounded-lg border-2 cursor-pointer transition flex-1"
                           style="border-color:#6b7280; background:#f9fafb;">
                        <input type="radio" name="registration_required" value="0" class="hidden" id="reg-no"
                               {{ old('registration_required', '0') === '0' ? 'checked' : '' }}
                               onchange="toggleRegFields()">
                        <i class="fas fa-times-circle text-gray-500"></i>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">No Registration</p>
                            <p class="text-xs text-gray-500">Open attendance, no sign-up needed</p>
                        </div>
                    </label>
                    <label id="lbl-reg-yes"
                           class="flex items-center gap-2 px-4 py-2.5 rounded-lg border-2 cursor-pointer transition flex-1"
                           style="border-color:#e5e7eb; background:#fff;">
                        <input type="radio" name="registration_required" value="1" class="hidden" id="reg-yes"
                               {{ old('registration_required') === '1' ? 'checked' : '' }}
                               onchange="toggleRegFields()">
                        <i class="fas fa-clipboard-list text-blue-600"></i>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Require Registration</p>
                            <p class="text-xs text-gray-500">People sign up via the website</p>
                        </div>
                    </label>
                </div>

                <div id="capacity-row" class="mt-3 {{ old('registration_required') === '1' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                        Capacity <span class="text-gray-400 font-normal normal-case">(leave blank for unlimited)</span>
                    </label>
                    <input type="number" name="capacity" value="{{ old('capacity') }}" min="1"
                           placeholder="e.g. 200"
                           class="w-40 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">
                    @error('capacity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Description --}}
            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Description <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
                <textarea name="description" rows="4"
                          placeholder="Event details visible on the public website…"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">{{ old('description', $calendar->notes) }}</textarea>
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                        class="px-6 py-2 rounded-lg text-white text-sm font-semibold flex items-center gap-2" style="background:#1e3a6e;">
                    <i class="fas fa-paper-plane"></i> Publish Event
                </button>
                <a href="{{ route('admin.calendar.index', ['year' => $calendar->calendar_year, 'month' => $calendar->start_date->month]) }}"
                   class="px-6 py-2 rounded-lg border border-gray-300 text-sm text-gray-600 hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRegFields() {
    const yes = document.getElementById('reg-yes').checked;
    document.getElementById('capacity-row').classList.toggle('hidden', !yes);
    document.getElementById('lbl-reg-yes').style.cssText = yes ? 'border-color:#1e3a6e;background:#eff6ff;' : 'border-color:#e5e7eb;background:#fff;';
    document.getElementById('lbl-reg-no').style.cssText  = !yes ? 'border-color:#6b7280;background:#f9fafb;' : 'border-color:#e5e7eb;background:#fff;';
}
// Set initial state on page load
toggleRegFields();

function previewPoster(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('preview-img').src = e.target.result;
        document.getElementById('preview-img').classList.remove('hidden');
        document.getElementById('drop-placeholder').classList.add('hidden');
    };
    reader.readAsDataURL(input.files[0]);
}

const dz = document.getElementById('drop-zone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('border-blue-400', 'bg-blue-50'); });
dz.addEventListener('dragleave', () => { dz.classList.remove('border-blue-400', 'bg-blue-50'); });
dz.addEventListener('drop', e => {
    e.preventDefault();
    dz.classList.remove('border-blue-400', 'bg-blue-50');
    const file = e.dataTransfer.files[0];
    if (file) {
        const inp = document.getElementById('poster-input');
        const dt = new DataTransfer();
        dt.items.add(file);
        inp.files = dt.files;
        previewPoster(inp);
    }
});
</script>
@endsection
