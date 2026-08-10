@extends('layouts.app')

@section('title', 'About Us — Chrisco Upper Room Fellowship')

@section('content')

{{-- HERO --}}
<section class="py-16 text-white text-center" style="background: linear-gradient(135deg, #0a1f44 0%, #1a3a6b 100%);">
    <div class="max-w-3xl mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-bold mb-3" style="font-family: 'Playfair Display', serif;">About Us</h1>
        <p class="text-xl italic text-yellow-400">"Where God Dwells"</p>
        <p class="text-gray-300 mt-3">Chrisco Upper Room Fellowship — Nairobi, Kenya</p>
    </div>
</section>

{{-- MAIN LAYOUT --}}
<section class="py-10" style="background:#f8fafc; min-height:60vh;">
    <div class="max-w-6xl mx-auto px-4 flex flex-col lg:flex-row gap-8">

        {{-- SIDEBAR NAV --}}
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                <nav id="about-nav">
                    <button onclick="showSection('who-we-are')" id="btn-who-we-are"
                            class="about-nav-btn w-full flex items-center gap-3 px-5 py-4 text-sm font-semibold text-left border-b border-gray-100 transition">
                        <i class="fas fa-church w-5 text-center"></i> Who We Are
                    </button>
                    <button onclick="showSection('leadership')" id="btn-leadership"
                            class="about-nav-btn w-full flex items-center gap-3 px-5 py-4 text-sm font-semibold text-left transition">
                        <i class="fas fa-users w-5 text-center"></i> Church Leadership
                    </button>
                </nav>
            </div>
        </aside>

        {{-- CONTENT AREA --}}
        <div class="flex-1 min-w-0">

            {{-- WHO WE ARE PANEL --}}
            <div id="section-who-we-are" class="about-section">

                {{-- About / Bio --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6">
                    <h2 class="text-2xl font-bold mb-1" style="color:#0a1f44; font-family:'Playfair Display',serif;">Who We Are</h2>
                    <div class="w-12 h-1 rounded mb-5" style="background:#c0392b;"></div>
                    @if($whoWeAre)
                        <div class="text-gray-700 leading-relaxed">{!! nl2br(e($whoWeAre)) !!}</div>
                    @else
                        <p class="text-gray-400 italic">Church information coming soon.</p>
                    @endif
                </div>

                {{-- Vision & Mission --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 border-t-4" style="border-top-color:#0a1f44;">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background:#0a1f44;">
                                <i class="fas fa-eye text-yellow-400"></i>
                            </div>
                            <h3 class="text-xl font-bold" style="color:#0a1f44; font-family:'Playfair Display',serif;">Our Vision</h3>
                        </div>
                        @if($vision)
                            <p class="text-gray-700 leading-relaxed text-sm">{{ $vision }}</p>
                        @else
                            <p class="text-gray-400 italic text-sm">Vision statement coming soon.</p>
                        @endif
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 border-t-4" style="border-top-color:#c0392b;">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background:#c0392b;">
                                <i class="fas fa-bullseye text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold" style="color:#0a1f44; font-family:'Playfair Display',serif;">Our Mission</h3>
                        </div>
                        @if($mission)
                            <p class="text-gray-700 leading-relaxed text-sm">{{ $mission }}</p>
                        @else
                            <p class="text-gray-400 italic text-sm">Mission statement coming soon.</p>
                        @endif
                    </div>
                </div>

                {{-- Core Pillars --}}
                @if($pillars->count())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold mb-1" style="color:#0a1f44; font-family:'Playfair Display',serif;">Core Pillars</h2>
                    <div class="w-12 h-1 rounded mb-6" style="background:#c0392b;"></div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($pillars as $pillar)
                        <div class="text-center p-5 rounded-xl border border-gray-100 hover:shadow-md transition">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3" style="background:#0a1f44;">
                                <i class="{{ $pillar->icon }} text-yellow-400 text-xl"></i>
                            </div>
                            <h4 class="text-sm font-bold mb-1" style="color:#0a1f44;">{{ $pillar->title }}</h4>
                            <p class="text-gray-500 text-xs leading-relaxed">{{ $pillar->description }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>{{-- end who-we-are --}}

            {{-- CHURCH LEADERSHIP PANEL --}}
            <div id="section-leadership" class="about-section hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold mb-1" style="color:#0a1f44; font-family:'Playfair Display',serif;">Church Leadership</h2>
                    <div class="w-12 h-1 rounded mb-6" style="background:#c0392b;"></div>

                    @php $perPage = 10; $totalLeaders = $leaders->count(); $totalPages = (int) ceil($totalLeaders / $perPage); @endphp

                    @if($totalLeaders)
                        {{-- Pages --}}
                        @for($p = 1; $p <= $totalPages; $p++)
                        <div class="leader-page {{ $p > 1 ? 'hidden' : '' }}" data-page="{{ $p }}">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($leaders->slice(($p-1)*$perPage, $perPage) as $leader)
                                    @include('about._leader_card', ['leader' => $leader, 'featured' => false])
                                @endforeach
                            </div>
                        </div>
                        @endfor

                        {{-- Pagination --}}
                        @if($totalPages > 1)
                        <div class="flex items-center justify-center gap-2 mt-8">
                            <button onclick="prevLeaderPage()" id="leader-prev"
                                    class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 disabled:opacity-40" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="text-sm text-gray-500">
                                Page <span id="leader-page-num">1</span> of {{ $totalPages }}
                            </span>
                            <button onclick="nextLeaderPage()" id="leader-next"
                                    class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-600 hover:bg-gray-50">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        @endif
                    @else
                        <p class="text-center text-gray-400 italic py-10">Leadership information coming soon.</p>
                    @endif
                </div>
            </div>{{-- end leadership --}}

        </div>{{-- end content --}}
    </div>
</section>

<style>
.about-nav-btn { color: #4b5563; }
.about-nav-btn:hover { background: #f3f4f6; color: #0a1f44; }
.about-nav-btn.active { background: #0a1f44; color: #fff; }
.about-nav-btn.active i { color: #f0a500; }
</style>

<script>
let leaderCurrentPage = 1;
const leaderTotalPages = {{ $totalPages ?? 1 }};

function showSection(name) {
    document.querySelectorAll('.about-section').forEach(s => s.classList.add('hidden'));
    document.querySelectorAll('.about-nav-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('section-' + name).classList.remove('hidden');
    document.getElementById('btn-' + name).classList.add('active');
    window.location.hash = name;
}

function showLeaderPage(p) {
    document.querySelectorAll('.leader-page').forEach(el => el.classList.add('hidden'));
    const page = document.querySelector(`.leader-page[data-page="${p}"]`);
    if (page) page.classList.remove('hidden');
    document.getElementById('leader-page-num').textContent = p;
    document.getElementById('leader-prev').disabled = p <= 1;
    document.getElementById('leader-next').disabled = p >= leaderTotalPages;
    leaderCurrentPage = p;
}

function prevLeaderPage() { if (leaderCurrentPage > 1) showLeaderPage(leaderCurrentPage - 1); }
function nextLeaderPage() { if (leaderCurrentPage < leaderTotalPages) showLeaderPage(leaderCurrentPage + 1); }

document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash.replace('#', '');
    showSection(['who-we-are','leadership'].includes(hash) ? hash : 'who-we-are');
});
</script>

@endsection
