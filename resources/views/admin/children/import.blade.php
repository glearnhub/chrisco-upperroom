@extends('layouts.admin')

@section('title', 'Import Children')
@section('page-title', 'Members (Children)')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold" style="color:#0a1f44;">Import Children from Excel</h1>
        <p class="text-gray-500 text-sm mt-1">Upload an .xlsx or .xls file to bulk-add children records</p>
    </div>
    <a href="{{ route('admin.children.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to List
    </a>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg text-sm font-semibold text-green-800 bg-green-100 border border-green-200">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 px-4 py-3 rounded-lg text-sm text-red-800 bg-red-50 border border-red-200">
    <p class="font-semibold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i>Please fix the following:</p>
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Upload form --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-base font-bold mb-4" style="color:#0a1f44;">
            <i class="fas fa-file-upload mr-2" style="color:#7c3aed;"></i>Upload File
        </h2>
        <form method="POST" action="{{ route('admin.children.import.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center mb-4 hover:border-purple-400 transition-colors"
                 id="drop-zone">
                <i class="fas fa-file-excel text-4xl mb-3 block" style="color:#1d6f42;"></i>
                <p class="text-gray-600 font-semibold mb-1">Drag & drop your Excel file here</p>
                <p class="text-gray-400 text-sm mb-3">or click to browse</p>
                <input type="file" name="file" id="file-input" accept=".xlsx,.xls"
                       class="hidden" required onchange="showFileName(this)">
                <button type="button" onclick="document.getElementById('file-input').click()"
                        class="px-4 py-2 rounded-lg text-sm font-semibold border border-gray-300 hover:bg-gray-50">
                    Choose File
                </button>
                <p id="file-name" class="mt-2 text-sm font-medium text-purple-700 hidden"></p>
            </div>

            <button type="submit"
                    class="w-full py-3 rounded-lg text-sm font-bold text-white flex items-center justify-center gap-2"
                    style="background:#7c3aed;">
                <i class="fas fa-upload"></i> Import Children
            </button>
        </form>
    </div>

    {{-- Column format guide --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-base font-bold mb-4" style="color:#0a1f44;">
            <i class="fas fa-table mr-2" style="color:#c0392b;"></i>Expected Excel Format
        </h2>
        <p class="text-sm text-gray-500 mb-3">
            The first row should be a header row. Data starts from the row where Column A is a number.
        </p>
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr style="background:#0a1f44; color:white;">
                        <th class="px-3 py-2 text-left border border-blue-900">Column</th>
                        <th class="px-3 py-2 text-left border border-blue-900">Field</th>
                        <th class="px-3 py-2 text-left border border-blue-900">Required</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach([
                        ['A', '#', 'Yes (number)'],
                        ['B', 'First Name', 'Yes'],
                        ['C', 'Middle Name', 'No'],
                        ['D', 'Last Name', 'Yes'],
                        ['E', 'Gender (male/female)', 'No'],
                        ['F', 'Date of Birth (dd/mm/yyyy)', 'No'],
                        ['G', 'Sunday School Class', 'No'],
                        ['H', 'Parent 1 Name', 'No'],
                        ['I', 'Parent 1 Contact', 'No'],
                        ['J', 'Parent 2 Name', 'No'],
                        ['K', 'Parent 2 Contact', 'No'],
                        ['L', 'Notes', 'No'],
                    ] as [$col, $field, $req])
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-1.5 font-bold" style="color:#7c3aed;">{{ $col }}</td>
                        <td class="px-3 py-1.5 text-gray-700">{{ $field }}</td>
                        <td class="px-3 py-1.5">
                            @if(str_starts_with($req, 'Yes'))
                                <span class="px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">{{ $req }}</span>
                            @else
                                <span class="text-gray-400">Optional</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 p-3 rounded-lg text-xs text-blue-800 bg-blue-50 border border-blue-100">
            <i class="fas fa-info-circle mr-1"></i>
            <strong>Tip:</strong> You can export the current children list to Excel and use it as a template.
            <a href="{{ route('admin.children.index', ['export'=>'excel']) }}" class="underline font-semibold ml-1">Download template</a>
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
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-purple-500','bg-purple-50'); });
zone.addEventListener('dragleave', () => zone.classList.remove('border-purple-500','bg-purple-50'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('border-purple-500','bg-purple-50');
    const dt = e.dataTransfer;
    if (dt.files.length) {
        document.getElementById('file-input').files = dt.files;
        showFileName(document.getElementById('file-input'));
    }
});
</script>
@endpush

@endsection


