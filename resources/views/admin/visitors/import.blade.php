@extends('layouts.admin')

@section('title', 'Import Visitors')

@section('content')
<div class="p-6 max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.visitors.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold" style="color:#0a1f44;">Import Visitors</h1>
            <p class="text-sm text-gray-500">Upload an Excel file to bulk-add visitor records</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm text-blue-800">
            <p class="font-semibold mb-1"><i class="fas fa-info-circle mr-1"></i> Format requirements:</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                <li>Column A: Full Name (required)</li>
                <li>Column B: Gender (Male / Female / Other)</li>
                <li>Column C: Residence</li>
                <li>Column D: Occupation</li>
                <li>Column E: Phone (required)</li>
                <li>Column F: Email</li>
                <li>Row 1 = headers (skipped). Data starts at row 2.</li>
            </ul>
            <a href="{{ route('admin.visitors.import.template') }}" class="inline-flex items-center gap-1 mt-2 text-blue-700 font-semibold hover:underline text-xs">
                <i class="fas fa-download"></i> Download Template
            </a>
        </div>

        <form method="POST" action="{{ route('admin.visitors.import.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Excel File (.xlsx)</label>
                <input type="file" name="file" accept=".xlsx,.xls" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.visitors.index') }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded text-white font-semibold text-sm"
                        style="background:#0a1f44;">
                    <i class="fas fa-file-upload"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
