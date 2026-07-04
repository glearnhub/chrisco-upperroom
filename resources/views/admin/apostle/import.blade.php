@extends('layouts.admin')

@section('title', 'Import Teachings')
@section('page-title', 'Import Teachings')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color:#0a1f44;">Import Apostle Das Teachings</h1>
    <a href="{{ route('admin.apostle.index') }}" class="btn-navy px-4 py-2 text-sm">
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

        <form method="POST" action="{{ route('admin.apostle.import.store') }}" enctype="multipart/form-data">
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

        <p class="text-sm text-gray-600 mb-4">Your spreadsheet must have the following columns in order (no header styling required):</p>

        <div class="overflow-x-auto mb-5">
            <table class="w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                <thead style="background:#0a1f44; color:white;">
                    <tr>
                        <th class="px-3 py-2 text-left">Column</th>
                        <th class="px-3 py-2 text-left">Field</th>
                        <th class="px-3 py-2 text-left">Required</th>
                        <th class="px-3 py-2 text-left">Example</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="bg-gray-50">
                        <td class="px-3 py-2 font-bold" style="color:#f0a500;">A</td>
                        <td class="px-3 py-2">Title</td>
                        <td class="px-3 py-2"><span class="text-red-500 font-semibold">Yes</span></td>
                        <td class="px-3 py-2 text-gray-400">Faith that Moves Mountains</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-bold" style="color:#f0a500;">B</td>
                        <td class="px-3 py-2">YouTube URL</td>
                        <td class="px-3 py-2"><span class="text-red-500 font-semibold">Yes</span></td>
                        <td class="px-3 py-2 text-gray-400">https://youtu.be/abc123</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-3 py-2 font-bold" style="color:#f0a500;">C</td>
                        <td class="px-3 py-2">Category</td>
                        <td class="px-3 py-2 text-gray-400">No</td>
                        <td class="px-3 py-2 text-gray-400">Prayer & Intercession</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-bold" style="color:#f0a500;">D</td>
                        <td class="px-3 py-2">Description</td>
                        <td class="px-3 py-2 text-gray-400">No</td>
                        <td class="px-3 py-2 text-gray-400">A powerful message on...</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-3 py-2 font-bold" style="color:#f0a500;">E</td>
                        <td class="px-3 py-2">Status</td>
                        <td class="px-3 py-2 text-gray-400">No</td>
                        <td class="px-3 py-2 text-gray-400">published <em>or</em> draft</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-xs text-blue-700">
            <i class="fas fa-info-circle mr-1"></i>
            <strong>Tips:</strong>
            <ul class="mt-1 list-disc list-inside space-y-1">
                <li>Row 1 must be the header row (A, B, C, D, E labels or any text).</li>
                <li>New categories in column C are created automatically.</li>
                <li>Rows missing Title or YouTube URL are skipped.</li>
                <li>Status defaults to <em>draft</em> if left blank or invalid.</li>
            </ul>
        </div>

        {{-- Download Template --}}
        <a href="{{ route('admin.apostle.import.template') }}"
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
