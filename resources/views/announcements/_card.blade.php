@php $catColors = \App\Models\Announcement::CATEGORY_COLORS; $color = $catColors[$ann->category] ?? '#0a1f44'; @endphp
<div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow duration-200 overflow-hidden flex flex-col {{ $isPinned ? 'ring-2 ring-yellow-300' : '' }}">

    @if($ann->image)
        <a href="{{ route('announcements.show', $ann) }}">
            <img src="{{ asset('storage/' . $ann->image) }}" alt="{{ $ann->title }}"
                class="w-full h-44 object-cover">
        </a>
    @else
        <div class="h-2 w-full" style="background: {{ $color }};"></div>
    @endif

    <div class="p-5 flex flex-col flex-1">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold px-2.5 py-1 rounded-full text-white"
                style="background: {{ $color }};">
                {{ \App\Models\Announcement::CATEGORIES[$ann->category] ?? ucfirst($ann->category) }}
            </span>
            @if($isPinned)
                <i class="fas fa-thumbtack text-yellow-400 text-sm" title="Pinned"></i>
            @endif
        </div>

        <h3 class="font-bold text-gray-800 text-lg leading-snug mb-2">
            <a href="{{ route('announcements.show', $ann) }}" class="hover:underline" style="color: #0a1f44;">
                {{ $ann->title }}
            </a>
        </h3>

        <p class="text-gray-500 text-sm flex-1 leading-relaxed">
            {{ Str::limit(strip_tags($ann->body), 150) }}
        </p>

        <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
            <span class="text-xs text-gray-400">
                <i class="fas fa-calendar-alt mr-1"></i>
                {{ $ann->created_at->format('M d, Y') }}
            </span>
            <span class="text-xs text-gray-400">
                <i class="fas fa-church mr-1"></i>Chrisco Upper Room
            </span>
            <a href="{{ route('announcements.show', $ann) }}"
                class="text-xs font-semibold hover:opacity-80 transition-opacity"
                style="color: {{ $color }};">
                Read More <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>
