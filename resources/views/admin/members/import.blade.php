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
        <h2 class="font-bold text-lg mb-1" style="color: #0a1f44;">Supported Column Headers</h2>
        <p class="text-xs text-gray-500 mb-4">All columns are optional except <strong>Email</strong>. Use these exact names in your file's first row. Yes/No columns accept: Yes, No, Y, N, 1, 0.</p>

        @php
        $groups = [
            'Basic Info' => [
                'First Name', 'Middle Name', 'Last Name', 'Gender', 'Marital Status',
                'DOB', 'Phone Number', 'Email', 'Address',
            ],
            'Location' => [
                'County of Residence', 'Sub County', 'Sub Location/Estate',
            ],
            'Faith & Membership' => [
                'Month and year when of Salvation',
                'Born Again',
                'Baptized', 'Baptism Date',
                'Date of Joining CUR',
                'Committed Member', 'Month and year Committed',
                'In Commitment Class',
                'Member Type', 'Office',
            ],
            'Ministry' => [
                'Department', 'Department 2', 'Department 3',
                'Career/Occupation',
                'Do you belong to a Home Cell?', 'If Yes, which one?',
                'Are you assigned to any Deacon or Deaconess?', 'If yes, mention their name',
            ],
            'Next of Kin' => [
                'Next of Kin that can be reached in case of an emergency',
                'Relationship with the next of Kin',
                'Phone Number of the Next of Kin',
                'Second Next of Kin', 'Next of Kin 2 Relationship', 'Next of Kin 2 Phone',
            ],
            'Transfer' => [
                'Transfer Type', 'Transfer Church',
            ],
            'Medical' => [
                'Has Medical Condition', 'Medical Conditions', 'Medications',
                'Allergies', 'Emergency Medical Contact', 'Emergency Medical Phone',
                'Special Needs',
            ],
        ];
        @endphp

        <div class="space-y-4 text-xs max-h-[60vh] overflow-y-auto pr-1">
            @foreach($groups as $group => $cols)
            <div>
                <p class="font-bold text-gray-500 uppercase tracking-wide mb-1" style="font-size:9px;">{{ $group }}</p>
                <ul class="space-y-0.5">
                    @foreach($cols as $col)
                    <li class="text-gray-700 flex items-start gap-1.5">
                        <i class="fas fa-minus text-gray-300 mt-0.5 flex-shrink-0" style="font-size:8px;"></i>
                        {{ $col }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
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


