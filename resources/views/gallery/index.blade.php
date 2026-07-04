@extends('layouts.app')

@section('title', 'Gallery — Chrisco Upper Room Fellowship')

@push('styles')
<style>
    .gallery-grid { columns: 2; column-gap: 1rem; }
    @media(min-width: 640px) { .gallery-grid { columns: 3; } }
    @media(min-width: 1024px) { .gallery-grid { columns: 4; } }
    .gallery-item { break-inside: avoid; margin-bottom: 1rem; position: relative; overflow: hidden; border-radius: 8px; cursor: pointer; }
    .gallery-item img { width: 100%; display: block; transition: transform 0.3s; }
    .gallery-item:hover img { transform: scale(1.04); }
    .gallery-item .overlay { position: absolute; inset: 0; background: rgba(10,31,68,0); transition: background 0.3s; display: flex; align-items: flex-end; padding: 0.75rem; }
    .gallery-item:hover .overlay { background: rgba(10,31,68,0.55); }
    .gallery-item .caption { color: white; font-size: 0.78rem; opacity: 0; transition: opacity 0.3s; }
    .gallery-item:hover .caption { opacity: 1; }

    /* Lightbox */
    #lightbox { display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.93); align-items:center; justify-content:center; }
    #lightbox.open { display:flex; }
    #lightbox img { max-width:90vw; max-height:88vh; object-fit:contain; border-radius:6px; box-shadow:0 8px 40px rgba(0,0,0,0.6); }
    #lightbox-close { position:absolute; top:1rem; right:1.5rem; color:white; font-size:2rem; cursor:pointer; line-height:1; }
    #lightbox-prev, #lightbox-next { position:absolute; top:50%; transform:translateY(-50%); color:white; font-size:2rem; cursor:pointer; padding:1rem; background:rgba(255,255,255,0.08); border-radius:50%; }
    #lightbox-prev { left:1rem; }
    #lightbox-next { right:1rem; }
    #lightbox-caption { position:absolute; bottom:1rem; left:50%; transform:translateX(-50%); color:#e2e8f0; font-size:0.85rem; text-align:center; max-width:60vw; }
</style>
@endpush

@section('content')

<section style="background: #0a1f44; min-height: 220px; display:flex; align-items:center;">
    <div class="max-w-7xl mx-auto px-4 text-center w-full py-8">
        <i class="fas fa-images text-5xl mb-3" style="color: #f0a500;"></i>
        <h1 class="text-4xl font-bold text-white mb-2">Gallery</h1>
        <p class="text-gray-300">Moments captured at Chrisco Upper Room Fellowship</p>
        @include('partials.resources-subnav')
    </div>
</section>

<section class="py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">

        @if($categories->count() > 1)
        {{-- Category Filters --}}
        <div class="flex flex-wrap gap-2 mb-8 justify-center">
            <button onclick="filterGallery('all')" class="filter-btn active px-4 py-1.5 rounded-full text-sm font-semibold border-2 transition-colors" data-cat="all">All</button>
            @foreach($categories as $cat)
            <button onclick="filterGallery('{{ $cat }}')" class="filter-btn px-4 py-1.5 rounded-full text-sm font-semibold border-2 transition-colors" data-cat="{{ $cat }}">{{ $cat }}</button>
            @endforeach
        </div>
        @endif

        @if($items->count())
            {{-- Masonry Grid --}}
            <div class="gallery-grid" id="gallery-grid">
                @foreach($items->flatten() as $index => $item)
                <div class="gallery-item" data-cat="{{ $item->category }}" onclick="openLightbox({{ $index }})">
                    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title ?? $item->caption ?? 'Gallery' }}" loading="lazy">
                    <div class="overlay">
                        <div class="caption">
                            @if($item->title)<p class="font-semibold">{{ $item->title }}</p>@endif
                            @if($item->caption)<p class="opacity-80">{{ $item->caption }}</p>@endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-20">
                <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No photos yet. Check back soon.</p>
            </div>
        @endif
    </div>
</section>

{{-- Lightbox --}}
<div id="lightbox">
    <span id="lightbox-close" onclick="closeLightbox()">&times;</span>
    <span id="lightbox-prev" onclick="changeLightbox(-1)"><i class="fas fa-chevron-left"></i></span>
    <img id="lightbox-img" src="" alt="">
    <span id="lightbox-next" onclick="changeLightbox(1)"><i class="fas fa-chevron-right"></i></span>
    <p id="lightbox-caption"></p>
</div>

@push('scripts')
<script>
    // Gallery images data
    const galleryData = [
        @foreach($items->flatten() as $item)
        {
            src: "{{ Storage::url($item->image) }}",
            title: "{{ addslashes($item->title ?? '') }}",
            caption: "{{ addslashes($item->caption ?? '') }}",
            cat: "{{ $item->category }}"
        },
        @endforeach
    ];

    let visibleIndices = galleryData.map((_, i) => i);
    let currentIndex = 0;

    // Filter
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.style.borderColor = '#0a1f44';
        btn.style.color = '#0a1f44';
        btn.style.background = 'white';
    });
    document.querySelector('.filter-btn.active').style.background = '#0a1f44';
    document.querySelector('.filter-btn.active').style.color = 'white';

    function filterGallery(cat) {
        filterBtns.forEach(b => {
            b.classList.remove('active');
            b.style.background = 'white';
            b.style.color = '#0a1f44';
        });
        const active = document.querySelector(`.filter-btn[data-cat="${cat}"]`);
        if (active) { active.classList.add('active'); active.style.background = '#0a1f44'; active.style.color = 'white'; }

        document.querySelectorAll('.gallery-item').forEach(el => {
            el.style.display = (cat === 'all' || el.dataset.cat === cat) ? 'block' : 'none';
        });
        visibleIndices = galleryData.map((d, i) => (cat === 'all' || d.cat === cat) ? i : -1).filter(i => i >= 0);
    }

    // Lightbox
    function openLightbox(idx) {
        currentIndex = visibleIndices.indexOf(idx);
        if (currentIndex === -1) currentIndex = 0;
        showLightboxImage();
        document.getElementById('lightbox').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('open');
        document.body.style.overflow = '';
    }
    function changeLightbox(dir) {
        currentIndex = (currentIndex + dir + visibleIndices.length) % visibleIndices.length;
        showLightboxImage();
    }
    function showLightboxImage() {
        const d = galleryData[visibleIndices[currentIndex]];
        document.getElementById('lightbox-img').src = d.src;
        document.getElementById('lightbox-caption').textContent = [d.title, d.caption].filter(Boolean).join(' — ');
    }

    // Close on backdrop click
    document.getElementById('lightbox').addEventListener('click', function(e) {
        if (e.target === this) closeLightbox();
    });
    // Arrow keys
    document.addEventListener('keydown', e => {
        if (!document.getElementById('lightbox').classList.contains('open')) return;
        if (e.key === 'ArrowLeft') changeLightbox(-1);
        if (e.key === 'ArrowRight') changeLightbox(1);
        if (e.key === 'Escape') closeLightbox();
    });
</script>
@endpush

@endsection



