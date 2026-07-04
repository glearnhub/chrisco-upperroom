@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Dashboard Overview</h1>
    <p class="text-gray-500 text-sm">Welcome back, {{ auth()->user()->name }}</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <i class="fas fa-users text-2xl mb-2" style="color: #0a1f44;"></i>
        <p class="text-2xl font-bold" style="color: #0a1f44;">{{ $totalMembers ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-1">Members</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <i class="fas fa-bible text-2xl mb-2" style="color: #c0392b;"></i>
        <p class="text-2xl font-bold" style="color: #0a1f44;">{{ $totalSermons ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-1">Sermons</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <i class="fas fa-chart-line text-2xl mb-2" style="color: #f0a500;"></i>
        <p class="text-2xl font-bold" style="color: #0a1f44;">{{ number_format($totalVisits ?? 0) }}</p>
        <p class="text-xs text-gray-500 mt-1">Unique Visitors</p>
        <p class="text-xs font-semibold mt-0.5" style="color:#f0a500;">{{ $todayVisits ?? 0 }} today</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <i class="fas fa-calendar-alt text-2xl mb-2" style="color: #0a1f44;"></i>
        <p class="text-2xl font-bold" style="color: #0a1f44;">{{ $upcomingEvents ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-1">Events</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <i class="fas fa-praying-hands text-2xl mb-2" style="color: #c0392b;"></i>
        <p class="text-2xl font-bold" style="color: #0a1f44;">{{ $pendingPrayers ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-1">Pending Prayers</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <i class="fas fa-broadcast-tower text-2xl mb-2" style="{{ ($activeLivestream ?? false) ? 'color: #c0392b;' : 'color: #9ca3af;' }}"></i>
        <p class="text-lg font-bold" style="color: #0a1f44;">{{ ($activeLivestream ?? false) ? 'LIVE' : 'OFF' }}</p>
        <p class="text-xs text-gray-500 mt-1">Livestream</p>
    </div>
</div>

{{-- Visit Summary Bar --}}
<div class="bg-white rounded-xl shadow p-4 mb-6 flex flex-wrap gap-6 items-center">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:#fff3cd;">
            <i class="fas fa-chart-line" style="color:#f0a500;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400">All Time</p>
            <p class="font-bold text-gray-800">{{ number_format($totalVisits) }} visits</p>
        </div>
    </div>
    <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:#e8f4fd;">
            <i class="fas fa-sun" style="color:#3b82f6;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400">Today</p>
            <p class="font-bold text-gray-800">{{ $todayVisits }} visits</p>
        </div>
    </div>
    <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:#f0fdf4;">
            <i class="fas fa-calendar-week" style="color:#16a34a;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400">Last 7 Days</p>
            <p class="font-bold text-gray-800">{{ $weekVisits }} visits</p>
        </div>
    </div>
    <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:#f5f0ff;">
            <i class="fas fa-calendar-alt" style="color:#7c3aed;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400">Last 365 Days</p>
            <p class="font-bold text-gray-800">{{ number_format($yearVisits) }} visits</p>
        </div>
    </div>
    <p class="ml-auto text-xs text-gray-400 hidden md:block"><i class="fas fa-info-circle mr-1"></i>Counts unique visitors by session. Repeat visits on the same day are not counted twice.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Recent Members --}}
    <div class="bg-white rounded-xl shadow">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="font-bold text-gray-800">Recent Members</h2>
            <a href="{{ route('admin.members.index') }}" class="text-sm text-blue-600 hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            @if(isset($recentMembers) && $recentMembers->count())
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Name</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Email</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recentMembers as $m)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-gray-700 font-medium">{{ $m->name }}</td>
                                <td class="px-4 py-2 text-gray-500 text-xs">{{ $m->email }}</td>
                                <td class="px-4 py-2 text-gray-400 text-xs">{{ \Carbon\Carbon::parse($m->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-400 text-sm text-center py-6">No members yet.</p>
            @endif
        </div>
    </div>

    {{-- Most Clicked Pages --}}
    <div class="bg-white rounded-xl shadow">
        <div class="px-6 py-4 border-b">
            <h2 class="font-bold text-gray-800">Most Visited Pages</h2>
            <p class="text-xs text-gray-400 mt-0.5">Top 7 pages by total page views</p>
        </div>
        <div class="p-5 space-y-3">
            @php
                $pageLabels = [
                    '/'                  => 'Home',
                    '/sermons'           => 'Sermons',
                    '/livestream'        => 'Livestream',
                    '/events'            => 'Events',
                    '/about'             => 'About Us',
                    '/give'              => 'Giving',
                    '/gallery'           => 'Gallery',
                    '/prayer'            => 'Prayer Request',
                    '/announcements'     => 'Announcements',
                    '/apostle-teachings' => 'Apostle Das Teachings',
                    '/teachings'         => 'Teachings',
                    '/resources'         => 'Books & Articles',
                    '/verify'            => 'Member Verify',
                ];
                $maxHits = $topPages->max() ?: 1;
            @endphp
            @forelse($topPages as $path => $hits)
                @php
                    $label = $pageLabels[$path] ?? ucwords(str_replace(['/', '-'], [' ', ' '], ltrim($path, '/')));
                    $pct   = round(($hits / $maxHits) * 100);
                    $icons = ['/' => 'fa-home', '/sermons' => 'fa-bible', '/livestream' => 'fa-broadcast-tower', '/events' => 'fa-calendar-alt', '/give' => 'fa-hand-holding-heart', '/gallery' => 'fa-images', '/prayer' => 'fa-praying-hands', '/about' => 'fa-church', '/announcements' => 'fa-bullhorn', '/apostle-teachings' => 'fa-video', '/teachings' => 'fa-book-open', '/resources' => 'fa-file-pdf', '/verify' => 'fa-id-card'];
                    $icon  = $icons[$path] ?? 'fa-link';
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-700 flex items-center gap-2">
                            <i class="fas {{ $icon }} w-4 text-center" style="color:#0a1f44;"></i>
                            {{ $label }}
                        </span>
                        <span class="text-xs font-bold" style="color:#0a1f44;">{{ number_format($hits) }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full" style="width:{{ $pct }}%; background: linear-gradient(to right, #0a1f44, #4a6fa5);"></div>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-sm text-center py-6">No visit data yet.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- Detailed Statistics --}}
<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- Geographic Distribution --}}
    <div class="bg-white rounded-xl shadow">
        <div class="px-5 py-4 border-b flex items-center gap-2">
            <i class="fas fa-globe" style="color:#0a1f44;"></i>
            <div>
                <h2 class="font-bold text-gray-800 text-sm">Geographic Distribution</h2>
                <p class="text-xs text-gray-400">Unique visitors by country</p>
            </div>
        </div>
        <div class="p-5 space-y-3">
            @php $geoTotal = $geoStats->sum('total') ?: 1; @endphp
            @forelse($geoStats as $geo)
                @php $pct = round(($geo->total / $geoTotal) * 100); @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-700 flex items-center gap-2">
                            <span class="text-base">{{ $geo->country_code ? strtolower($geo->country_code) : '🌍' }}</span>
                            {{ $geo->country }}
                        </span>
                        <span class="text-xs font-bold text-gray-600">{{ $geo->total }} <span class="text-gray-400 font-normal">({{ $pct }}%)</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full" style="width:{{ $pct }}%; background:#f0a500;"></div>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-sm text-center py-6">
                    <i class="fas fa-globe text-3xl mb-2 block text-gray-300"></i>
                    No geo data yet.<br><span class="text-xs">Visitors outside localhost will appear here.</span>
                </p>
            @endforelse
        </div>
    </div>

    {{-- Gender Distribution --}}
    <div class="bg-white rounded-xl shadow">
        <div class="px-5 py-4 border-b flex items-center gap-2">
            <i class="fas fa-venus-mars" style="color:#c0392b;"></i>
            <div>
                <h2 class="font-bold text-gray-800 text-sm">Gender Distribution</h2>
                <p class="text-xs text-gray-400">Based on registered members</p>
            </div>
        </div>
        <div class="p-5">
            @php
                $genderTotal = $genderStats->sum() ?: 1;
                $genderColors = ['male' => '#0a1f44', 'female' => '#c0392b', 'other' => '#f0a500'];
                $genderIcons  = ['male' => 'fa-mars', 'female' => 'fa-venus', 'other' => 'fa-genderless'];
            @endphp
            @if($genderStats->isNotEmpty())
                {{-- Donut-style visual --}}
                <div class="flex justify-center mb-5">
                    <div class="relative w-32 h-32">
                        <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                            @php
                                $offset = 0;
                                $colors = ['#0a1f44','#c0392b','#f0a500','#6b7280'];
                                $ci = 0;
                            @endphp
                            @foreach($genderStats as $gender => $count)
                                @php
                                    $slice = ($count / $genderTotal) * 100;
                                    $color = $genderColors[strtolower($gender)] ?? $colors[$ci % count($colors)];
                                    $ci++;
                                @endphp
                                <circle cx="18" cy="18" r="15.9155"
                                    fill="transparent"
                                    stroke="{{ $color }}"
                                    stroke-width="4"
                                    stroke-dasharray="{{ $slice }} {{ 100 - $slice }}"
                                    stroke-dashoffset="{{ -$offset }}"/>
                                @php $offset += $slice; @endphp
                            @endforeach
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-xl font-bold" style="color:#0a1f44;">{{ $genderTotal }}</p>
                                <p class="text-xs text-gray-400">Total</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    @foreach($genderStats as $gender => $count)
                        @php
                            $color = $genderColors[strtolower($gender)] ?? '#6b7280';
                            $icon  = $genderIcons[strtolower($gender)] ?? 'fa-circle';
                            $pct   = round(($count / $genderTotal) * 100);
                        @endphp
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-sm text-gray-700">
                                <i class="fas {{ $icon }}" style="color:{{ $color }};"></i>
                                {{ ucfirst($gender) }}
                            </span>
                            <span class="text-xs font-bold" style="color:{{ $color }};">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm text-center py-8"><i class="fas fa-venus-mars text-3xl mb-2 block text-gray-300"></i>No gender data available.</p>
            @endif
        </div>
    </div>

    {{-- Age Distribution --}}
    <div class="bg-white rounded-xl shadow">
        <div class="px-5 py-4 border-b flex items-center gap-2">
            <i class="fas fa-birthday-cake" style="color:#f0a500;"></i>
            <div>
                <h2 class="font-bold text-gray-800 text-sm">Age Distribution</h2>
                <p class="text-xs text-gray-400">Based on registered members</p>
            </div>
        </div>
        <div class="p-5 space-y-3">
            @php
                $ageTotal = $ageStats->sum() ?: 1;
                $ageMax   = $ageStats->max() ?: 1;
                $ageColors = ['Under 18'=>'#f0a500','18–25'=>'#0a1f44','26–35'=>'#c0392b','36–45'=>'#16a34a','46–55'=>'#7c3aed','56+'=>'#6b7280'];
            @endphp
            @foreach($ageStats as $range => $count)
                @php
                    $pct   = $ageMax ? round(($count / $ageMax) * 100) : 0;
                    $color = $ageColors[$range] ?? '#0a1f44';
                    $memberPct = $count > 0 ? round(($count / $ageTotal) * 100) : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-semibold text-gray-600">{{ $range }}</span>
                        <span class="text-xs font-bold" style="color:{{ $color }};">{{ $count }} <span class="text-gray-400 font-normal">({{ $memberPct }}%)</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all" style="width:{{ $pct }}%; background:{{ $color }};"></div>
                    </div>
                </div>
            @endforeach
            @if($ageTotal <= 1 && $ageStats->sum() === 0)
                <p class="text-gray-400 text-sm text-center py-4"><i class="fas fa-birthday-cake text-3xl mb-2 block text-gray-300"></i>No age data available.</p>
            @endif
        </div>
    </div>

</div>

@endsection


