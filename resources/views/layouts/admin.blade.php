<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IT Support') — Chrisco Upper Room</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

    <style>
        :root { --navy: #0a1f44; --red: #c0392b; --gold: #f0a500; }
        html { background: #0a1f44; overscroll-behavior-y: none; }
        body { font-family: 'Lato', sans-serif; background: #f1f5f9; }
        h1, h2, h3, h4 { font-family: 'Playfair Display', serif; }
        .sidebar { width: 240px; height: 100vh; background: #0a1f44; position: fixed; top: 0; left: 0; z-index: 40; display: flex; flex-direction: column; overflow: hidden; }
        .sidebar nav { flex: 1; overflow-y: auto; overflow-x: hidden; }
        .sidebar nav::-webkit-scrollbar { width: 4px; }
        .sidebar nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 2px; }
        .sidebar nav::-webkit-scrollbar-track { background: transparent; }
        .main-content { margin-left: 240px; min-height: 100vh; }
        .sidebar-link { display: flex; align-items: center; padding: 0.65rem 1.25rem; color: #cbd5e1; font-size: 0.875rem; transition: background 0.2s, color 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(255,255,255,0.1); color: #f0a500; }
        .sidebar-link i { width: 20px; margin-right: 10px; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar" id="admin-sidebar">
        <div class="px-4 py-3 border-b border-blue-900">
            <img src="{{ asset('images/logo.png') }}"
                 alt="Chrisco Upper Room Fellowship"
                 style="height:48px; width:auto; object-fit:contain; display:block;">
            <p class="text-xs mt-2" style="color:rgba(255,255,255,0.4);">IT Support Panel</p>
        </div>

        <nav class="mt-2">
            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>

            {{-- Media Group --}}
            <p class="px-4 pt-4 pb-1 text-xs font-bold uppercase tracking-widest" style="color: #4a6fa5;">Media</p>
            <a href="{{ route('admin.sermons.index') }}" class="sidebar-link {{ request()->routeIs('admin.sermons.*') ? 'active' : '' }}">
                <i class="fas fa-bible"></i> Sermons
            </a>
            <a href="{{ route('admin.teachings.index') }}" class="sidebar-link {{ request()->routeIs('admin.teachings.*') ? 'active' : '' }}">
                <i class="fas fa-book-open"></i> Teachings
            </a>
            <a href="{{ route('admin.resources.index') }}" class="sidebar-link {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}">
                <i class="fas fa-file-pdf"></i> Books & Articles
            </a>
            <a href="{{ route('admin.livestreams.index') }}" class="sidebar-link {{ request()->routeIs('admin.livestreams.*') ? 'active' : '' }}">
                <i class="fas fa-broadcast-tower"></i> Livestream
            </a>
            <a href="{{ route('admin.gallery.index') }}" class="sidebar-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                <i class="fas fa-images"></i> Gallery
            </a>

            {{-- Church Life Group --}}
            <p class="px-4 pt-4 pb-1 text-xs font-bold uppercase tracking-widest" style="color: #4a6fa5;">Church Life</p>
            <a href="{{ route('admin.events.index') }}" class="sidebar-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Events
            </a>
            <a href="{{ route('admin.announcements.index') }}" class="sidebar-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                <i class="fas fa-bullhorn"></i> Announcements
            </a>
            <a href="{{ route('admin.prayers.index') }}" class="sidebar-link {{ request()->routeIs('admin.prayers.*') ? 'active' : '' }}">
                <i class="fas fa-praying-hands"></i> Prayers
            </a>
            <a href="{{ route('admin.donations.index') }}" class="sidebar-link {{ request()->routeIs('admin.donations.*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-usd"></i> Givings
            </a>

            {{-- People & Admin Group --}}
            <p class="px-4 pt-4 pb-1 text-xs font-bold uppercase tracking-widest" style="color: #4a6fa5;">People</p>
            <a href="{{ route('admin.members.index') }}" class="sidebar-link {{ request()->routeIs('admin.members.index') || request()->routeIs('admin.members.show') || request()->routeIs('admin.members.create') || request()->routeIs('admin.members.edit') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Members (Adults)
            </a>

            <a href="{{ route('admin.children.index') }}" class="sidebar-link {{ request()->routeIs('admin.children.*') ? 'active' : '' }}">
                <i class="fas fa-child"></i> Members (Children)
            </a>
            <a href="{{ route('admin.about.index') }}" class="sidebar-link {{ request()->routeIs('admin.about.*') ? 'active' : '' }}">
                <i class="fas fa-church"></i> About Us
            </a>

            {{-- Reports Group --}}
            <p class="px-4 pt-4 pb-1 text-xs font-bold uppercase tracking-widest" style="color: #4a6fa5;">Reports</p>
            <a href="{{ route('admin.reports.membership') }}" class="sidebar-link {{ request()->routeIs('admin.reports.membership') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Full Membership
            </a>
            <a href="{{ route('admin.reports.leaders') }}" class="sidebar-link {{ request()->routeIs('admin.reports.leaders') ? 'active' : '' }}">
                <i class="fas fa-crown"></i> Leaders
            </a>
            <a href="{{ route('admin.reports.mentorship') }}" class="sidebar-link {{ request()->routeIs('admin.reports.mentorship') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i> Mentorship
            </a>
            <a href="{{ route('admin.reports.children-parent') }}" class="sidebar-link {{ request()->routeIs('admin.reports.children-parent') ? 'active' : '' }}">
                <i class="fas fa-child"></i> Children by Parent
            </a>
            <a href="{{ route('admin.reports.by-department') }}" class="sidebar-link {{ request()->routeIs('admin.reports.by-department') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i> By Department
            </a>
            <a href="{{ route('admin.reports.events') }}" class="sidebar-link {{ request()->routeIs('admin.reports.events*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i> Event Reports
            </a>

            <div class="mt-6 px-4 py-3 border-t border-gray-700">
                <a href="{{ route('home') }}" class="sidebar-link text-xs">
                    <i class="fas fa-globe"></i> View Website
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-link w-full text-left text-red-400 hover:text-red-300">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content flex flex-col">

        <!-- Top Bar -->
        <header style="background-color: #0a1f44; left: 240px;" class="fixed top-0 right-0 z-30 h-16 flex items-center justify-between px-6 shadow-md">
            <div class="flex items-center space-x-3">
                <button class="md:hidden text-white" onclick="document.getElementById('admin-sidebar').classList.toggle('open')">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="text-white font-semibold text-sm">@yield('page-title', 'IT Support')</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-gray-300 text-sm hidden sm:flex items-center">
                    <i class="fas fa-user-shield mr-1 text-yellow-400"></i>
                    {{ auth()->user()->name ?? 'Admin' }}
                </span>
                <form method="POST" action="{{ route('logout') }}" style="display:flex; align-items:center; margin:0;">
                    @csrf
                    <button type="submit" class="text-white text-sm px-4 rounded flex items-center gap-1" style="background: #c0392b; height:36px; line-height:1;">
                        <i class="fas fa-sign-out-alt"></i>Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6" style="margin-top: 64px;">

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex items-center justify-between">
                    <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded flex items-center justify-between">
                    <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @include('partials.print-dialog')
    @stack('scripts')
</body>
</html>



