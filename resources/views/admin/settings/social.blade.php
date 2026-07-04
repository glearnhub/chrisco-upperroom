@extends('layouts.admin')
@section('title', 'Social Media Settings')
@section('page-title', 'Social Media Settings')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-share-alt" style="color:#f0a500;"></i> Social Media Links
        </h2>

        <form method="POST" action="{{ route('admin.settings.social.update') }}">
            @csrf

            @foreach([
                'facebook'  => ['fab fa-facebook', 'Facebook', '#1877F2'],
                'instagram' => ['fab fa-instagram', 'Instagram', '#E1306C'],
                'youtube'   => ['fab fa-youtube', 'YouTube', '#FF0000'],
                'tiktok'    => ['fab fa-tiktok', 'TikTok', '#000000'],
            ] as $key => [$icon, $label, $color])
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    <i class="{{ $icon }} mr-1" style="color:{{ $color }};"></i> {{ $label }} URL
                </label>
                <input type="url" name="{{ $key }}"
                    value="{{ old($key, \App\Models\Setting::get('social.'.$key)) }}"
                    placeholder="https://..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                @error($key)
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endforeach

            <button type="submit" class="px-6 py-2 rounded-lg text-white text-sm font-semibold" style="background:#0a1f44;">
                <i class="fas fa-save mr-1"></i> Save Changes
            </button>
        </form>
    </div>
</div>
@endsection
