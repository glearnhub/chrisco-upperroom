<div class="bg-white rounded-2xl shadow-md overflow-hidden text-center {{ isset($featured) && $featured ? 'max-w-xs mx-auto' : '' }}">
    @if($leader->photo)
        <img src="{{ asset('storage/' . $leader->photo) }}" alt="{{ $leader->name }}"
             class="w-full h-52 object-cover object-top">
    @else
        <div class="w-full h-52 flex items-center justify-center" style="background: linear-gradient(135deg, #0a1f44, #1a3a6b);">
            <i class="fas fa-user text-white text-5xl opacity-30"></i>
        </div>
    @endif
    <div class="p-5">
        <h4 class="text-lg font-bold" style="color: #0a1f44; font-family: 'Playfair Display', serif;">{{ $leader->name }}</h4>
        <p class="text-sm font-semibold mt-1" style="color: #c0392b;">{{ $leader->title }}</p>
        @if($leader->bio)
            <p class="text-gray-500 text-sm mt-3 leading-relaxed">{{ Str::limit($leader->bio, 120) }}</p>
        @endif
    </div>
</div>


