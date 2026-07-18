@extends('layouts.admin')

@section('title', 'Edit Visitor')

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.visitors.show', $visitor) }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold" style="color:#0a1f44;">Edit Visitor</h1>
            <p class="text-sm text-gray-500">{{ $visitor->full_name }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded">
        <p class="font-semibold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Please fix the following:</p>
        <ul class="list-disc list-inside text-sm space-y-0.5">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.visitors.update', $visitor) }}">
            @csrf @method('PUT')
            @include('admin.visitors._form')

            <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.visitors.show', $visitor) }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded text-white font-semibold text-sm"
                        style="background:#0a1f44;">
                    <i class="fas fa-save"></i> Update Visitor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
