@extends('layouts.admin')
@section('title', 'Import Calendar Events')

@section('content')
<div class="p-3 sm:p-6 max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.calendar.index') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold" style="color:#1e3a6e;">
                <i class="fas fa-file-excel mr-2" style="color:#16a34a;"></i>Import Calendar Events
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Upload an Excel or CSV file to bulk-add events to the calendar</p>
        </div>
    </div>

    @if(session('import_errors') && count(session('import_errors')))
    <div class="mb-4 bg-yellow-50 border border-yellow-300 rounded-lg px-4 py-3">
        <p class="font-semibold text-yellow-800 text-sm mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Some rows were skipped:</p>
        <ul class="list-disc list-inside text-xs text-yellow-700 space-y-0.5">
            @foreach(session('import_errors') as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Download template --}}
    <div class="mb-5 bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 flex items-center justify-between gap-4">
        <div>
            <p class="font-semibold text-blue-800 text-sm"><i class="fas fa-info-circle mr-1"></i> Use our template for best results</p>
            <p class="text-xs text-blue-600 mt-0.5">Columns: Title · Start Date · End Date · Category · Color · Notes</p>
        </div>
        <a href="{{ route('admin.calendar.import.template') }}"
           class="flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#16a34a;">
            <i class="fas fa-download"></i> Download Template
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.calendar.import.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- File upload --}}
            <div class="mb-5">
                <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Excel / CSV File *</label>
                <div id="drop-zone"
                     class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-blue-400 transition"
                     onclick="document.getElementById('file-input').click()"
                     ondragover="event.preventDefault(); this.classList.add('border-blue-400','bg-blue-50')"
                     ondragleave="this.classList.remove('border-blue-400','bg-blue-50')"
                     ondrop="handleDrop(event)">
                    <i class="fas fa-file-excel text-4xl mb-2" style="color:#16a34a;"></i>
                    <p class="text-gray-600 font-semibold text-sm" id="drop-label">Drop your file here, or <span class="underline" style="color:#1e3a6e;">browse</span></p>
                    <p class="text-xs text-gray-400 mt-1">Accepts .xlsx, .xls, .csv — max 5 MB</p>
                </div>
                <input type="file" id="file-input" name="file" accept=".xlsx,.xls,.csv" class="hidden" onchange="showFileName(this)">
                @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                {{-- Calendar year --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Calendar Year *</label>
                    <select name="calendar_year" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200">
                        @foreach(range(now()->year - 1, now()->year + 4) as $y)
                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Used when the file rows don't include a year</p>
                </div>

                {{-- Published --}}
                <div class="flex flex-col justify-end">
                    <div class="flex items-center gap-2 mb-1">
                        <input type="checkbox" name="is_published" id="is_published" value="1" checked
                               class="rounded border-gray-300 w-4 h-4">
                        <label for="is_published" class="text-sm font-semibold text-gray-700">Mark events as Published</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="overwrite" id="overwrite" value="1"
                               class="rounded border-gray-300 w-4 h-4">
                        <label for="overwrite" class="text-sm text-gray-700">Overwrite duplicates <span class="text-gray-400 text-xs">(same title + date)</span></label>
                    </div>
                </div>
            </div>

            {{-- Category reference --}}
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-xs font-bold uppercase text-gray-500 mb-2">Valid Category Values</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $key => $cat)
                    <span class="text-xs text-white font-semibold px-2.5 py-1 rounded" style="background:{{ $cat['color'] }};">
                        {{ $key }}
                    </span>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg text-white text-sm font-semibold flex items-center gap-2" style="background:#1e3a6e;">
                    <i class="fas fa-upload"></i> Import Events
                </button>
                <a href="{{ route('admin.calendar.index') }}"
                   class="px-6 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-600 hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    {{-- Format reference --}}
    <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b" style="background:#1e3a6e;">
            <h3 class="text-white font-semibold text-sm"><i class="fas fa-table mr-1"></i> Expected File Format</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-bold text-gray-600">Column</th>
                        <th class="px-4 py-2 text-left font-bold text-gray-600">Required</th>
                        <th class="px-4 py-2 text-left font-bold text-gray-600">Example</th>
                        <th class="px-4 py-2 text-left font-bold text-gray-600">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    <tr><td class="px-4 py-2 font-semibold">Title</td><td class="px-4 py-2 text-green-600">Yes</td><td class="px-4 py-2">Sunday School Sunday</td><td class="px-4 py-2">Name of the event</td></tr>
                    <tr class="bg-gray-50"><td class="px-4 py-2 font-semibold">Start Date</td><td class="px-4 py-2 text-green-600">Yes</td><td class="px-4 py-2">2026-07-26</td><td class="px-4 py-2">YYYY-MM-DD or DD/MM/YYYY</td></tr>
                    <tr><td class="px-4 py-2 font-semibold">End Date</td><td class="px-4 py-2 text-gray-400">No</td><td class="px-4 py-2">2026-07-28</td><td class="px-4 py-2">Leave blank for single-day</td></tr>
                    <tr class="bg-gray-50"><td class="px-4 py-2 font-semibold">Category</td><td class="px-4 py-2 text-gray-400">No</td><td class="px-4 py-2">general</td><td class="px-4 py-2">See valid values above</td></tr>
                    <tr><td class="px-4 py-2 font-semibold">Color</td><td class="px-4 py-2 text-gray-400">No</td><td class="px-4 py-2">#16a34a</td><td class="px-4 py-2">Hex code — auto-set from category if blank</td></tr>
                    <tr class="bg-gray-50"><td class="px-4 py-2 font-semibold">Notes</td><td class="px-4 py-2 text-gray-400">No</td><td class="px-4 py-2">Annual outreach event</td><td class="px-4 py-2">Optional description</td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function showFileName(input) {
    if (input.files.length) {
        document.getElementById('drop-label').innerHTML =
            '<i class="fas fa-check-circle" style="color:#16a34a;"></i> ' + input.files[0].name;
    }
}
function handleDrop(e) {
    e.preventDefault();
    const zone = document.getElementById('drop-zone');
    zone.classList.remove('border-blue-400','bg-blue-50');
    const dt = e.dataTransfer;
    if (dt.files.length) {
        const input = document.getElementById('file-input');
        input.files = dt.files;
        showFileName(input);
    }
}
</script>
@endsection
