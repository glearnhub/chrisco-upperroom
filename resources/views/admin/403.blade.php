@extends('layouts.admin')
@section('title', 'Access Denied')
@section('page-title', 'Access Denied')

@section('content')
<div class="flex flex-col items-center justify-center" style="min-height:60vh;">
    <div class="text-center">
        <div class="mb-6" style="font-size:5rem; color:#c0392b; opacity:0.15;">
            <i class="fas fa-lock"></i>
        </div>
        <h1 class="text-4xl font-bold text-gray-800 mb-2">403</h1>
        <h2 class="text-xl font-semibold text-gray-600 mb-4">Access Denied</h2>
        <p class="text-gray-500 mb-2">You do not have permission to access this page.</p>
        @isset($permission)
        <p class="text-xs text-gray-400 mb-6">Required permission: <code class="bg-gray-100 px-2 py-0.5 rounded">{{ $permission }}</code></p>
        @endisset
        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-white font-semibold" style="background:#0a1f44;">
            <i class="fas fa-home"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection
