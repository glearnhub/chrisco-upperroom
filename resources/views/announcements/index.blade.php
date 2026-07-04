@extends('layouts.app')

@section('title', 'Announcements')

@section('content')

{{-- Hero --}}
<section style="background: #0a1f44; min-height: 220px; display:flex; align-items:center;">
    <div class="max-w-6xl mx-auto px-4 text-center w-full py-8">
        <i class="fas fa-bullhorn text-5xl mb-3" style="color: #f0a500;"></i>
        <h1 class="text-4xl font-bold text-white mb-2" style="font-family: 'Playfair Display', serif;">
            Announcements
        </h1>
        <p class="text-gray-300">Stay updated with the latest news from Chrisco Upper Room Fellowship</p>
        @include('partials.updates-subnav')
    </div>
</section>

{{-- Category Filter --}}
<section style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;" class="sticky top-[72px] z-10">
    <div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap gap-2 items-center">
        <a href="{{ route('announcements.index') }}"
            class="px-4 py-1.5 rounded-full text-sm font-semibold transition-all
            {{ !$category ? 'text-white' : 'bg-white text-gray-600 border border-gray-300 hover:border-gray-400' }}"
            style="{{ !$category ? 'background:#0a1f44;' : '' }}">
            All
        </a>
        @foreach($categories as $key => $label)
            @php $colors = \App\Models\Announcement::CATEGORY_COLORS; @endphp
            <a href="{{ route('announcements.index', ['category' => $key]) }}"
                class="px-4 py-1.5 rounded-full text-sm font-semibold transition-all
                {{ $category === $key ? 'text-white' : 'bg-white text-gray-600 border border-gray-300 hover:border-gray-400' }}"
                style="{{ $category === $key ? 'background:' . $colors[$key] . ';' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</section>

{{-- Announcements Grid --}}
<section style="background: #f8fafc;" class="py-12">
    <div class="max-w-6xl mx-auto px-4">

        @if($announcements->count())
            {{-- Pinned section --}}
            @php $pinned = $announcements->filter(fn($a) => $a->is_pinned); @endphp
            @if($pinned->count())
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-thumbtack text-yellow-500"></i>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-500">Pinned</h2>
                    </div>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($pinned as $ann)
                            @include('announcements._card', ['ann' => $ann, 'isPinned' => true])
                        @endforeach
                    </div>
                </div>
                @php $rest = $announcements->filter(fn($a) => !$a->is_pinned); @endphp
                @if($rest->count())
                    <hr class="mb-8 border-gray-200">
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($rest as $ann)
                            @include('announcements._card', ['ann' => $ann, 'isPinned' => false])
                        @endforeach
                    </div>
                @endif
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($announcements as $ann)
                        @include('announcements._card', ['ann' => $ann, 'isPinned' => false])
                    @endforeach
                </div>
            @endif

            <div class="mt-10">{{ $announcements->links() }}</div>

        @else
            <div class="text-center py-24 text-gray-400">
                <i class="fas fa-bullhorn text-6xl mb-4 block"></i>
                <p class="text-xl font-semibold">No announcements at this time</p>
                <p class="text-sm mt-1">Check back soon for updates from the church.</p>
            </div>
        @endif

    </div>
</section>

@endsection
