<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IT Support') — Chrisco Upper Room</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome 6 (local – avoids Edge Tracking Prevention blocking CDN) -->
    <link rel="stylesheet" href="{{ asset('fa/css/all.min.css') }}">

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
        .sidebar-link-disabled { display:flex; align-items:center; padding:0.65rem 1.25rem; color:rgba(255,255,255,0.25); font-size:0.875rem; cursor:not-allowed; position:relative; }
        .sidebar-link-disabled i.main-icon { width:20px; margin-right:10px; }
        .sidebar-link-disabled .lock-icon { margin-left:auto; font-size:0.65rem; color:rgba(255,255,255,0.2); }
        .sidebar-link-disabled:hover { background:rgba(255,0,0,0.05); }
        .sidebar-link-disabled .no-perm-tip { display:none; position:absolute; left:calc(100% + 6px); top:50%; transform:translateY(-50%); background:#1e293b; color:#f0a500; font-size:0.7rem; white-space:nowrap; padding:4px 8px; border-radius:4px; z-index:100; border:1px solid rgba(240,165,0,0.3); pointer-events:none; }
        .sidebar-link-disabled:hover .no-perm-tip { display:block; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .topbar { left: 0 !important; }
            .topbar-title { font-size: 0.8rem; max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .topbar-logout-text { display: none; }
            .topbar-logout-btn { width: 36px; padding: 0; justify-content: center; }
        }

        /* ══ Global Mobile Responsive ══════════════════════ */
        @media (max-width: 640px) {
            main.flex-1 { padding: 1rem !important; }

            /* Prevent iOS zoom on inputs */
            input, select, textarea { font-size: 16px !important; }

            /* Page headers: let title + buttons stack */
            main .flex.items-center.justify-between,
            main .flex.items-start.justify-between { flex-wrap: wrap; row-gap: 0.5rem; }

            /* Button groups inside headers */
            main .flex.items-center.gap-2,
            main .flex.items-center.gap-3,
            main .flex.items-center.space-x-2,
            main .flex.items-center.space-x-3 { flex-wrap: wrap; }

            /* Stat grids: max 2-col on phones */
            main .grid.grid-cols-3,
            main .grid.grid-cols-4,
            main .grid.grid-cols-5 { grid-template-columns: repeat(2, 1fr) !important; }

            /* Cards: reduce padding */
            .bg-white.rounded-xl { padding: 1rem !important; }
            .bg-white.rounded-2xl { padding: 1rem !important; }

            /* Form grids: collapse to 1 col */
            main .grid.grid-cols-2:not(.no-mobile-collapse),
            main .grid.grid-cols-3:not(.no-mobile-collapse) { grid-template-columns: 1fr !important; }

            /* Table wrapper auto-scroll (applied by JS) */
            .table-scroll-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%; }
            .table-scroll-wrap table { min-width: 500px; }
        }

        /* ══ Global Confirm Modal ═══════════════════════════ */
        .g-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.55);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            z-index: 10000; padding: 20px;
        }
        .g-overlay.hidden { display: none; }
        .g-box {
            background: #fff; border-radius: 18px;
            padding: 28px 24px 22px;
            width: 100%; max-width: 360px;
            text-align: center;
            box-shadow: 0 24px 64px rgba(0,0,0,.25);
            animation: gBoxIn .18s ease;
        }
        @keyframes gBoxIn { from { opacity:0; transform:scale(.94) translateY(8px); } to { opacity:1; transform:none; } }
        .g-icon { width:54px;height:54px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:22px; }
        .g-icon.danger  { background:#fee2e2;color:#ef4444; }
        .g-icon.warning { background:#fef3c7;color:#f59e0b; }
        .g-icon.info    { background:#dbeafe;color:#3b82f6; }
        .g-box h3 { font-size:17px;font-weight:800;color:#0a1f44;margin-bottom:7px;font-family:'Playfair Display',serif; }
        .g-box p  { font-size:13px;color:#6b7280;margin-bottom:22px;line-height:1.55; }
        .g-actions { display:flex;gap:10px; }
        .g-actions button { flex:1;padding:12px;border-radius:12px;font-size:14px;font-weight:700;font-family:'Lato',sans-serif;cursor:pointer;border:none;transition:opacity .15s; }
        .g-actions button:hover { opacity:.85; }
        .g-btn-cancel  { background:#f3f4f6;color:#374151; }
        .g-btn-ok.danger  { background:#ef4444;color:#fff; }
        .g-btn-ok.warning { background:#f59e0b;color:#fff; }
        .g-btn-ok.info    { background:#0a1f44;color:#fff; }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar" id="admin-sidebar">
        <div class="px-4 py-3 border-b border-blue-900" style="background: linear-gradient(to bottom, #ffffff 0%, #c8d8f0 30%, #0a1f44 100%);">
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

            @php $u = auth()->user(); @endphp

            {{-- Helper macro: outputs enabled link or disabled padlock span --}}
            {{-- Used inline below --}}

            {{-- Media Group --}}
            <p class="px-4 pt-4 pb-1 text-xs font-bold uppercase tracking-widest" style="color: #4a6fa5;">Media</p>

            {{-- Resources dropdown: Sermons, Teachings, Books & Articles, Apostle Das --}}
            @php
            $resourcesOpen = request()->routeIs('admin.sermons.*') || request()->routeIs('admin.teachings.*') || request()->routeIs('admin.resources.*') || request()->routeIs('admin.apostle.*');
            $canResources  = $u->hasPermission('sermons.view') || $u->hasPermission('teachings.view') || $u->hasPermission('resources.view');
            @endphp
            @if($canResources)
            <button onclick="toggleSidebarGroup('resources-group', this)"
                    class="sidebar-link w-full text-left flex items-center justify-between {{ $resourcesOpen ? 'active' : '' }}"
                    style="background:none; border:none; cursor:pointer;">
                <span><i class="fas fa-folder-open main-icon"></i> Resources</span>
                <i class="fas fa-chevron-down text-xs {{ $resourcesOpen ? 'rotate-180' : '' }}" style="margin-left:auto; opacity:.6;"></i>
            </button>
            <div id="resources-group" style="{{ $resourcesOpen ? 'display:block;' : 'display:none;' }} padding-left:12px;">
                @if($u->hasPermission('sermons.view'))
                <a href="{{ route('admin.sermons.index') }}" class="sidebar-link text-sm {{ request()->routeIs('admin.sermons.*') ? 'active' : '' }}">
                    <i class="fas fa-bible main-icon"></i> Sermons
                </a>
                @endif
                @if($u->hasPermission('teachings.view'))
                <a href="{{ route('admin.teachings.index') }}" class="sidebar-link text-sm {{ request()->routeIs('admin.teachings.*') ? 'active' : '' }}">
                    <i class="fas fa-book-open main-icon"></i> Teachings
                </a>
                @endif
                @if($u->hasPermission('resources.view'))
                <a href="{{ route('admin.resources.index') }}" class="sidebar-link text-sm {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}">
                    <i class="fas fa-file-pdf main-icon"></i> Books & Articles
                </a>
                @endif
                @if($u->hasPermission('sermons.view'))
                <a href="{{ route('admin.apostle.index') }}" class="sidebar-link text-sm {{ request()->routeIs('admin.apostle.*') ? 'active' : '' }}">
                    <i class="fas fa-video main-icon"></i> Apostle Das
                </a>
                @endif
            </div>
            @else
            <span class="sidebar-link-disabled">
                <i class="fas fa-folder-open main-icon"></i> Resources
                <i class="fas fa-lock lock-icon"></i>
                <span class="no-perm-tip">You have no permissions</span>
            </span>
            @endif

            @if($u->hasPermission('livestream.view'))
            <a href="{{ route('admin.livestreams.index') }}" class="sidebar-link {{ request()->routeIs('admin.livestreams.*') ? 'active' : '' }}">
                <i class="fas fa-broadcast-tower main-icon"></i> Livestream
            </a>
            @else
            <span class="sidebar-link-disabled">
                <i class="fas fa-broadcast-tower main-icon"></i> Livestream
                <i class="fas fa-lock lock-icon"></i>
                <span class="no-perm-tip">You have no permissions</span>
            </span>
            @endif

            @if($u->hasPermission('gallery.view'))
            <a href="{{ route('admin.gallery.index') }}" class="sidebar-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                <i class="fas fa-images main-icon"></i> Gallery
            </a>
            @else
            <span class="sidebar-link-disabled">
                <i class="fas fa-images main-icon"></i> Gallery
                <i class="fas fa-lock lock-icon"></i>
                <span class="no-perm-tip">You have no permissions</span>
            </span>
            @endif

            {{-- Church Life Group --}}
            <p class="px-4 pt-4 pb-1 text-xs font-bold uppercase tracking-widest" style="color: #4a6fa5;">Church Life</p>

            {{-- Updates dropdown: Events, Church Calendar, Announcements --}}
            @php
            $updatesOpen = request()->routeIs('admin.events.*') || request()->routeIs('admin.calendar.*') || request()->routeIs('admin.announcements.*');
            @endphp
            <button onclick="toggleSidebarGroup('updates-group', this)"
                    class="sidebar-link w-full text-left flex items-center justify-between {{ $updatesOpen ? 'active' : '' }}"
                    style="background:none; border:none; cursor:pointer;">
                <span><i class="fas fa-bell main-icon"></i> Updates</span>
                <i class="fas fa-chevron-down text-xs {{ $updatesOpen ? 'rotate-180' : '' }}" style="margin-left:auto; opacity:.6;"></i>
            </button>
            <div id="updates-group" style="{{ $updatesOpen ? 'display:block;' : 'display:none;' }} padding-left:12px;">
                @if($u->hasPermission('events.view'))
                <a href="{{ route('admin.events.index') }}" class="sidebar-link text-sm {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt main-icon"></i> Events
                </a>
                @else
                <span class="sidebar-link-disabled text-sm">
                    <i class="fas fa-calendar-alt main-icon"></i> Events
                    <i class="fas fa-lock lock-icon"></i>
                    <span class="no-perm-tip">You have no permissions</span>
                </span>
                @endif
                @if($u->hasPermission('calendar.manage'))
                <a href="{{ route('admin.calendar.index') }}" class="sidebar-link text-sm {{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-week main-icon"></i> Church Calendar
                </a>
                @endif
                @if($u->hasPermission('announcements.view'))
                <a href="{{ route('admin.announcements.index') }}" class="sidebar-link text-sm {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn main-icon"></i> Announcements
                </a>
                @else
                <span class="sidebar-link-disabled text-sm">
                    <i class="fas fa-bullhorn main-icon"></i> Announcements
                    <i class="fas fa-lock lock-icon"></i>
                    <span class="no-perm-tip">You have no permissions</span>
                </span>
                @endif
            </div>

            @if($u->hasPermission('prayers.view'))
            <a href="{{ route('admin.prayers.index') }}" class="sidebar-link {{ request()->routeIs('admin.prayers.*') ? 'active' : '' }}">
                <i class="fas fa-praying-hands main-icon"></i> Prayers
            </a>
            @else
            <span class="sidebar-link-disabled">
                <i class="fas fa-praying-hands main-icon"></i> Prayers
                <i class="fas fa-lock lock-icon"></i>
                <span class="no-perm-tip">You have no permissions</span>
            </span>
            @endif

            @if($u->hasPermission('givings.view'))
            <a href="{{ route('admin.donations.index') }}" class="sidebar-link {{ request()->routeIs('admin.donations.*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-usd main-icon"></i> Givings
            </a>
            @else
            <span class="sidebar-link-disabled">
                <i class="fas fa-hand-holding-usd main-icon"></i> Givings
                <i class="fas fa-lock lock-icon"></i>
                <span class="no-perm-tip">You have no permissions</span>
            </span>
            @endif

            {{-- People Group --}}
            <p class="px-4 pt-4 pb-1 text-xs font-bold uppercase tracking-widest" style="color: #4a6fa5;">People</p>

            @if($u->hasPermission('about.manage'))
            @php $aboutOpen = request()->routeIs('admin.about.*') || request()->routeIs('admin.settings.social.*'); @endphp
            <button onclick="toggleSidebarGroup('about-group', this)"
                    class="sidebar-link w-full text-left flex items-center justify-between {{ $aboutOpen ? 'active' : '' }}"
                    style="background:none; border:none; cursor:pointer;">
                <span><i class="fas fa-church main-icon"></i> About Us</span>
                <i class="fas fa-chevron-down text-xs transition-transform {{ $aboutOpen ? 'rotate-180' : '' }}" style="margin-left:auto; opacity:.6;"></i>
            </button>
            <div id="about-group" style="{{ $aboutOpen ? 'display:block;' : 'display:none;' }} padding-left:12px;">
                <a href="{{ route('admin.about.index') }}" class="sidebar-link text-sm {{ request()->routeIs('admin.about.*') ? 'active' : '' }}">
                    <i class="fas fa-info-circle main-icon"></i> Church Info
                </a>
                @if($u->isSuperAdmin())
                <a href="{{ route('admin.settings.social.index') }}" class="sidebar-link text-sm {{ request()->routeIs('admin.settings.social.*') ? 'active' : '' }}">
                    <i class="fas fa-share-alt main-icon"></i> Social Media
                </a>
                @endif
            </div>
            @else
            <span class="sidebar-link-disabled">
                <i class="fas fa-church main-icon"></i> About Us
                <i class="fas fa-lock lock-icon"></i>
                <span class="no-perm-tip">You have no permissions</span>
            </span>
            @endif

            @if($u->hasPermission('members.view'))
            <a href="{{ route('admin.members.index') }}" class="sidebar-link {{ request()->routeIs('admin.members.index') || request()->routeIs('admin.members.show') || request()->routeIs('admin.members.create') || request()->routeIs('admin.members.edit') ? 'active' : '' }}">
                <i class="fas fa-users main-icon"></i> Members (Adults)
            </a>
            @endif
            @if($u->hasPermission('children.view'))
            <a href="{{ route('admin.children.index') }}" class="sidebar-link {{ request()->routeIs('admin.children.*') && !request()->routeIs('admin.children.attendance*') ? 'active' : '' }}">
                <i class="fas fa-child main-icon"></i> Members (Children)
            </a>
            <a href="{{ route('admin.children.attendance') }}" class="sidebar-link {{ request()->routeIs('admin.children.attendance') ? 'active' : '' }}" style="padding-left:2.5rem;">
                <i class="fas fa-camera-retro main-icon"></i> Child Attendance
            </a>
            @endif
            @if(!$u->hasPermission('members.view') && !$u->hasPermission('children.view'))
            <span class="sidebar-link-disabled">
                <i class="fas fa-users main-icon"></i> Members (Adults)
                <i class="fas fa-lock lock-icon"></i>
                <span class="no-perm-tip">You have no permissions</span>
            </span>
            <span class="sidebar-link-disabled">
                <i class="fas fa-child main-icon"></i> Members (Children)
                <i class="fas fa-lock lock-icon"></i>
                <span class="no-perm-tip">You have no permissions</span>
            </span>
            @endif

            @if($u->hasPermission('visitors.view'))
            <a href="{{ route('admin.visitors.index') }}" class="sidebar-link {{ request()->routeIs('admin.visitors.*') ? 'active' : '' }}">
                <i class="fas fa-user-clock main-icon"></i> Visitors
            </a>
            @else
            <span class="sidebar-link-disabled">
                <i class="fas fa-user-clock main-icon"></i> Visitors
                <i class="fas fa-lock lock-icon"></i>
                <span class="no-perm-tip">You have no permissions</span>
            </span>
            @endif

            {{-- Attendance --}}
            @if($u->hasPermission('attendance.view'))
            <a href="{{ route('admin.attendance.index') }}" class="sidebar-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check main-icon"></i> Attendance
            </a>
            @else
            <span class="sidebar-link-disabled">
                <i class="fas fa-clipboard-check main-icon"></i> Attendance
                <i class="fas fa-lock lock-icon"></i>
                <span class="no-perm-tip">You have no permissions</span>
            </span>
            @endif

            {{-- Correction Requests --}}
            @if($u->hasPermission('members.view'))
            @php $pendingCorrections = \App\Models\CorrectionRequest::where('status','pending')->count(); @endphp
            <a href="{{ route('admin.corrections.index') }}" class="sidebar-link {{ request()->routeIs('admin.corrections.*') ? 'active' : '' }}" style="position:relative;">
                <i class="fas fa-edit main-icon"></i> Correction Requests
                @if($pendingCorrections)
                    <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:#c0392b; color:#fff; font-size:0.65rem; font-weight:700; min-width:18px; height:18px; border-radius:9999px; display:flex; align-items:center; justify-content:center; padding:0 4px;">
                        <i class="fas fa-bell mr-0.5" style="font-size:0.55rem;"></i>{{ $pendingCorrections }}
                    </span>
                @endif
            </a>
            @else
            <span class="sidebar-link-disabled">
                <i class="fas fa-edit main-icon"></i> Correction Requests
                <i class="fas fa-lock lock-icon"></i>
                <span class="no-perm-tip">You have no permissions</span>
            </span>
            @endif

            {{-- Reports Group --}}
            <p class="px-4 pt-4 pb-1 text-xs font-bold uppercase tracking-widest" style="color: #4a6fa5;">Reports</p>

            @if($u->hasPermission('reports.membership') || $u->hasPermission('reports.children'))
            <a href="{{ route('admin.reports.membership') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-users main-icon"></i> Members
            </a>
            @else
            <span class="sidebar-link-disabled">
                <i class="fas fa-users main-icon"></i> Members
                <i class="fas fa-lock lock-icon"></i>
                <span class="no-perm-tip">You have no permissions</span>
            </span>
            @endif

            {{-- Settings Group — Super Admin only --}}
            @if($u->isSuperAdmin())
            <p class="px-4 pt-4 pb-1 text-xs font-bold uppercase tracking-widest" style="color: #4a6fa5;">Settings</p>
            <a href="{{ route('admin.settings.verify-access.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.verify-access.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check main-icon"></i> Verify Access
            </a>
            <a href="{{ route('admin.settings.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.users.*') ? 'active' : '' }}">
                <i class="fas fa-user-cog main-icon"></i> System Users
            </a>
            <a href="{{ route('admin.settings.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.roles.*') || request()->routeIs('admin.settings.permissions.*') ? 'active' : '' }}">
                <i class="fas fa-lock main-icon"></i> System Permissions
            </a>
            <a href="{{ route('admin.settings.logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.logs.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list main-icon"></i> System Logs
            </a>
            @endif

            <div class="mt-6 px-4 py-3 border-t border-gray-700">
                <a href="{{ route('admin.my-profile.edit') }}" class="sidebar-link {{ request()->routeIs('admin.my-profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user-circle main-icon"></i> My Profile
                </a>
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
        <header style="background-color: #0a1f44; left: 240px;" class="topbar fixed top-0 right-0 z-30 h-16 flex items-center justify-between px-4 md:px-6 shadow-md">
            <div class="flex items-center space-x-3">
                <button class="md:hidden text-white w-9 h-9 flex items-center justify-center rounded-lg hover:bg-white/10 transition-colors" onclick="document.getElementById('admin-sidebar').classList.toggle('open')">
                    <i class="fas fa-bars text-base"></i>
                </button>
                <span class="text-white font-semibold topbar-title text-sm">@yield('page-title', 'IT Support')</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="topbar-user hidden md:flex text-gray-300 text-sm items-center gap-1">
                    <i class="fas fa-user-shield text-yellow-400 text-xs"></i>
                    {{ auth()->user()->name ?? 'Admin' }}
                </span>
                <form method="POST" action="{{ route('logout') }}" style="display:flex; align-items:center; margin:0;">
                    @csrf
                    <button type="submit" class="topbar-logout-btn text-white text-sm px-3 rounded-lg flex items-center gap-1.5" style="background: #c0392b; height:36px; line-height:1;">
                        <i class="fas fa-sign-out-alt"></i><span class="topbar-logout-text">Logout</span>
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

    <!-- Global Confirm Modal -->
    <div id="g-confirm-modal" class="g-overlay hidden" role="dialog" aria-modal="true">
        <div class="g-box">
            <div class="g-icon danger" id="g-icon"><i class="fas fa-triangle-exclamation"></i></div>
            <h3 id="g-title">Are you sure?</h3>
            <p id="g-msg"></p>
            <div class="g-actions">
                <button class="g-btn-cancel" id="g-cancel">Cancel</button>
                <button class="g-btn-ok danger" id="g-ok">Confirm</button>
            </div>
        </div>
    </div>

    @stack('scripts')
<script>
/* ── Sidebar toggle groups ───────────────── */
function toggleSidebarGroup(id, btn) {
    var group = document.getElementById(id);
    var icon  = btn.querySelector('.fa-chevron-down');
    var open  = group.style.display !== 'none';
    group.style.display = open ? 'none' : 'block';
    if (icon) icon.style.transform = open ? '' : 'rotate(180deg)';
}

/* ── Global Confirm Modal ────────────────── */
(function() {
    var modal  = document.getElementById('g-confirm-modal');
    var iconEl = document.getElementById('g-icon');
    var titleEl= document.getElementById('g-title');
    var msgEl  = document.getElementById('g-msg');
    var okBtn  = document.getElementById('g-ok');
    var cancelBtn = document.getElementById('g-cancel');
    var _resolve = null;

    var typeIcons = {
        danger:  'fa-triangle-exclamation',
        warning: 'fa-exclamation-circle',
        info:    'fa-info-circle'
    };

    window.gConfirm = function(opts) {
        var type = opts.type || 'danger';
        iconEl.className = 'g-icon ' + type;
        iconEl.innerHTML = '<i class="fas ' + (typeIcons[type] || typeIcons.danger) + '"></i>';
        titleEl.textContent = opts.title || 'Are you sure?';
        msgEl.textContent   = opts.msg   || '';
        okBtn.textContent   = opts.ok    || 'Confirm';
        okBtn.className     = 'g-btn-ok ' + type;
        modal.classList.remove('hidden');
        modal.querySelector('.g-box').style.animation = 'none';
        requestAnimationFrame(function() {
            modal.querySelector('.g-box').style.animation = '';
        });
        return new Promise(function(resolve) { _resolve = resolve; });
    };

    function close(val) {
        modal.classList.add('hidden');
        if (_resolve) { _resolve(val); _resolve = null; }
    }

    okBtn.addEventListener('click', function() { close(true); });
    cancelBtn.addEventListener('click', function() { close(false); });
    modal.addEventListener('click', function(e) { if (e.target === modal) close(false); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && !modal.classList.contains('hidden')) close(false); });

    /* Intercept data-confirm forms */
    document.addEventListener('submit', async function(e) {
        var form = e.target;
        var msg = form.getAttribute('data-confirm');
        if (!msg) return;
        e.preventDefault();
        var ok = await gConfirm({
            msg:  msg,
            type: form.getAttribute('data-confirm-type') || 'danger',
            ok:   form.getAttribute('data-confirm-ok')   || 'Confirm',
            title: form.getAttribute('data-confirm-title') || 'Are you sure?'
        });
        if (ok) { form.removeAttribute('data-confirm'); form.submit(); }
    }, true);

    /* Intercept data-confirm buttons/links that aren't inside forms handled above */
    document.addEventListener('click', async function(e) {
        var el = e.target.closest('[data-confirm]:not(form)');
        if (!el) return;
        e.preventDefault(); e.stopImmediatePropagation();
        var ok = await gConfirm({
            msg:  el.getAttribute('data-confirm'),
            type: el.getAttribute('data-confirm-type') || 'danger',
            ok:   el.getAttribute('data-confirm-ok')   || 'Confirm',
            title: el.getAttribute('data-confirm-title') || 'Are you sure?'
        });
        if (ok) {
            el.removeAttribute('data-confirm');
            el.click();
        }
    }, true);
})();

/* ── Auto-wrap tables for mobile scroll ─── */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('main table').forEach(function(t) {
        if (t.closest('.table-scroll-wrap') || t.closest('.overflow-x-auto')) return;
        var wrap = document.createElement('div');
        wrap.className = 'table-scroll-wrap';
        t.parentNode.insertBefore(wrap, t);
        wrap.appendChild(t);
    });
});
</script>
</body>
</html>



