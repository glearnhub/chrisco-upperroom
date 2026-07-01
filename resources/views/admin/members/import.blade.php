@extends('layouts.admin')

@section('title', 'Import Members')
@section('page-title', 'Import Members')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Import Members from Excel / CSV</h1>
    <a href="{{ route('admin.members.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back to Members
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Upload Form --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow p-8">
            <h2 class="font-bold text-lg mb-5" style="color: #0a1f44;">Upload File</h2>

            @if(session('import_errors') && count(session('import_errors')))
            <div class="mb-5 bg-yellow-50 border border-yellow-300 rounded-lg p-4">
                <p class="font-semibold text-yellow-800 mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Some rows were skipped:</p>
                <ul class="text-sm text-yellow-700 space-y-1 max-h-40 overflow-y-auto">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.members.import') }}" enctype="multipart/form-data">
                @csrf

                <div class="border-2 border-dashed border-gray-300 rounded-xl p-10 text-center mb-5 hover:border-blue-400 transition-colors" id="drop-zone">
                    <i class="fas fa-file-excel text-5xl mb-3" style="color: #16a34a;"></i>
                    <p class="text-gray-600 font-semibold mb-1">Drop your Excel or CSV file here</p>
                    <p class="text-gray-400 text-sm mb-4">or click to browse</p>
                    <input type="file" name="file" id="file-input" accept=".xlsx,.xls,.csv" class="hidden" required
                           onchange="showFileName(this)">
                    <label for="file-input" class="cursor-pointer btn-navy px-5 py-2 text-sm inline-block">
                        <i class="fas fa-folder-open mr-2"></i>Choose File
                    </label>
                    <p id="file-name" class="mt-3 text-sm text-blue-600 hidden"></p>
                    @error('file')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-5 text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Accepted formats:</strong> .xlsx, .xls, .csv — Max 10MB<br>
                    Members with duplicate emails will be skipped automatically.
                    Passwords are randomly generated (members can reset via login page).
                </div>

                <button type="submit" class="btn-red px-8 py-2 font-semibold w-full text-center">
                    <i class="fas fa-upload mr-2"></i>Import Members
                </button>
            </form>
        </div>
    </div>

    {{-- Column Guide --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-bold text-lg mb-4" style="color: #0a1f44;">Expected Column Headers</h2>
        <p class="text-xs text-gray-500 mb-3">Your file's first row must have these exact column headers (same as the Membership Details form):</p>
        <ol class="space-y-1.5 text-xs text-gray-700">
            @php
            $cols = [
                'First Name', 'Middle Name', 'Last Name', 'Gender', 'DOB',
                'Phone Number', 'Email', 'County of Residence', 'Sub County',
                'Sub Location/Estate', 'Month and year when of Salvation',
                'Date of Joining CUR', 'Month and year Committed',
                'Department', 'Career/Occupation',
                'Next of Kin that can be reached in case of an emergency',
                'Relationship with the next of Kin',
                'Phone Number of the Next of Kin',
                'Do you belong to a Home Cell?',
                'If Yes, which one?',
                'Are you assigned to any Deacon or Deaconess?',
                'If yes, mention their name',
            ];
            @endphp
            @foreach($cols as $i => $col)
            <li class="flex items-start space-x-2">
                <span class="w-5 h-5 rounded-full text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5"
                      style="background: #0a1f44; font-size: 9px;">{{ $i+1 }}</span>
                <span>{{ $col }}</span>
            </li>
            @endforeach
        </ol>
        <div class="mt-4 pt-4 border-t">
            <p class="text-xs text-gray-500"><strong>Tip:</strong> Use Yes/No for columns 19 and 21. The system will map them automatically.</p>
        </div>
    </div>

</div>

@push('scripts')
<script>
function showFileName(input) {
    const label = document.getElementById('file-name');
    if (input.files.length) {
        label.textContent = '✓ ' + input.files[0].name;
        label.classList.remove('hidden');
    }
}

// Drag & drop
const zone = document.getElementById('drop-zone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-blue-500', 'bg-blue-50'); });
zone.addEventListener('dragleave', () => zone.classList.remove('border-blue-500', 'bg-blue-50'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('border-blue-500', 'bg-blue-50');
    const input = document.getElementById('file-input');
    input.files = e.dataTransfer.files;
    showFileName(input);
});
</script>
@endpush

@endsection


