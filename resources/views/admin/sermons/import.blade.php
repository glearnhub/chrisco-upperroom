@extends('layouts.admin')

@section('title', 'Import Sermons')
@section('page-title', 'Import Sermons')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color:#0a1f44;">Import Sermons</h1>
    <a href="{{ route('admin.sermons.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Upload Form --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold mb-4" style="color:#0a1f44;">Upload Excel File</h2>

        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.sermons.import.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Excel / CSV File <span class="text-red-500">*</span>
                </label>
                <div id="drop-zone"
                    class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer transition hover:border-blue-400"
                    onclick="document.getElementById('file-input').click()">
                    <i class="fas fa-file-excel text-4xl mb-3" style="color:#1d6f42;"></i>
                    <p class="text-gray-500 text-sm">Click or drag & drop your file here</p>
                    <p class="text-gray-400 text-xs mt-1">.xlsx, .xls or .csv — max 5MB</p>
                    <p id="file-name" class="mt-3 text-sm font-semibold hidden" style="color:#0a1f44;"></p>
                </div>
                <input type="file" id="file-input" name="file" accept=".xlsx,.xls,.csv" class="hidden"
                    onchange="showFileName(this)">
                @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="btn-red w-full py-3 text-center font-bold">
                <i class="fas fa-upload mr-2"></i>Import Now
            </button>
        </form>
    </div>

    {{-- Instructions --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold mb-4" style="color:#0a1f44;">File Format Guide</h2>

        <p class="text-sm text-gray-600 mb-4">Your spreadsheet must have the following columns in order:</p>

        <div class="overflow-x-auto mb-5">
            <table class="w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                <thead style="background:#0a1f44; color:white;">
                    <tr>
                        <th class="px-3 py-2 text-left">Col</th>
                        <th class="px-3 py-2 text-left">Field</th>
                        <th class="px-3 py-2 text-left">Required</th>
                        <th class="px-3 py-2 text-left">Example</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="bg-gray-50"><td class="px-3 py-2 font-bold" style="color:#f0a500;">A</td><td class="px-3 py-2">Title</td><td class="px-3 py-2 text-red-500 font-semibold">Yes</td><td class="px-3 py-2 text-gray-400">Walking in the Spirit</td></tr>
                    <tr><td class="px-3 py-2 font-bold" style="color:#f0a500;">B</td><td class="px-3 py-2">Speaker</td><td class="px-3 py-2 text-red-500 font-semibold">Yes</td><td class="px-3 py-2 text-gray-400">Apostle Das</td></tr>
                    <tr class="bg-gray-50"><td class="px-3 py-2 font-bold" style="color:#f0a500;">C</td><td class="px-3 py-2">Date</td><td class="px-3 py-2 text-red-500 font-semibold">Yes</td><td class="px-3 py-2 text-gray-400">2026-07-04</td></tr>
                    <tr><td class="px-3 py-2 font-bold" style="color:#f0a500;">D</td><td class="px-3 py-2">Category slug</td><td class="px-3 py-2 text-gray-400">No</td><td class="px-3 py-2 text-gray-400">sunday-service</td></tr>
                    <tr class="bg-gray-50"><td class="px-3 py-2 font-bold" style="color:#f0a500;">E</td><td class="px-3 py-2">Scripture</td><td class="px-3 py-2 text-gray-400">No</td><td class="px-3 py-2 text-gray-400">John 3:16</td></tr>
                    <tr><td class="px-3 py-2 font-bold" style="color:#f0a500;">F</td><td class="px-3 py-2">Video URL</td><td class="px-3 py-2 text-gray-400">No</td><td class="px-3 py-2 text-gray-400">https://youtu.be/abc</td></tr>
                    <tr class="bg-gray-50"><td class="px-3 py-2 font-bold" style="color:#f0a500;">G</td><td class="px-3 py-2">Audio URL</td><td class="px-3 py-2 text-gray-400">No</td><td class="px-3 py-2 text-gray-400">https://...</td></tr>
                    <tr><td class="px-3 py-2 font-bold" style="color:#f0a500;">H</td><td class="px-3 py-2">Description</td><td class="px-3 py-2 text-gray-400">No</td><td class="px-3 py-2 text-gray-400">A message on...</td></tr>
                    <tr class="bg-gray-50"><td class="px-3 py-2 font-bold" style="color:#f0a500;">I</td><td class="px-3 py-2">Status</td><td class="px-3 py-2 text-gray-400">No</td><td class="px-3 py-2 text-gray-400">published / draft</td></tr>
                </tbody>
            </table>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-xs text-blue-700 mb-4">
            <i class="fas fa-info-circle mr-1"></i>
            <strong>Valid category slugs:</strong>
            <div class="mt-1 flex flex-wrap gap-1">
                @foreach(\App\Models\Sermon::CATEGORIES as $slug => $label)
                    <code class="bg-white border border-blue-200 px-1.5 py-0.5 rounded text-blue-800">{{ $slug }}</code>
                @endforeach
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3 text-xs text-yellow-700">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            Row 1 is the header — data starts from row 2. Rows missing Title, Speaker or Date are skipped.
        </div>

        <a href="{{ route('admin.sermons.import.template') }}"
           class="mt-5 flex items-center gap-2 text-sm font-semibold hover:underline" style="color:#0a1f44;">
            <i class="fas fa-download" style="color:#1d6f42;"></i>
            Download Sample Template (.xlsx)
        </a>
    </div>

</div>

@push('scripts')
<script>
function showFileName(input) {
    const name = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        name.textContent = input.files[0].name;
        name.classList.remove('hidden');
        document.getElementById('drop-zone').style.borderColor = '#0a1f44';
    }
}
const dropZone = document.getElementById('drop-zone');
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.style.borderColor = '#f0a500'; });
dropZone.addEventListener('dragleave', () => { dropZone.style.borderColor = '#d1d5db'; });
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    const input = document.getElementById('file-input');
    input.files = e.dataTransfer.files;
    showFileName(input);
});
</script>
@endpush

@endsection
