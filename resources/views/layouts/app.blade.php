<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Chrisco Upper Room Fellowship')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

    <style>
        :root { --navy: #0a1f44; --red: #c0392b; --gold: #f0a500; }
        html, body { margin: 0; padding: 0; }
        html { background: #0a1f44; overscroll-behavior-y: none; }
        body { font-family: 'Lato', sans-serif; }
        h1, h2, h3, h4 { font-family: 'Playfair Display', serif; }
        .btn-red { background: #c0392b; color: white; padding: 0.6rem 1.5rem; border-radius: 4px; display: inline-block; }
        .btn-red:hover { background: #a93226; }
        .btn-navy { background: #0a1f44; color: white; padding: 0.6rem 1.5rem; border-radius: 4px; display: inline-block; }
        .btn-navy:hover { background: #152d5e; }
        .nav-link { transition: color 0.2s; }
        .nav-link:hover { color: #f0a500; }
        #mobile-menu { display: none; }
        #mobile-menu.open { display: block; }
        .dropdown { position: relative; }
        .dropdown-menu { display: none; position: absolute; top: 100%; left: 0; min-width: 180px; background: #0d2856; border-top: 2px solid #f0a500; box-shadow: 0 8px 24px rgba(0,0,0,0.3); z-index: 100; border-radius: 0 0 8px 8px; }
        .dropdown:hover .dropdown-menu { display: block; }
        .dropdown-menu a { display: flex; align-items: center; padding: 0.6rem 1rem; color: #cbd5e1; font-size: 0.8rem; transition: background 0.15s, color 0.15s; }
        .dropdown-menu a:hover { background: rgba(255,255,255,0.08); color: #f0a500; }
        .dropdown-menu a i { width: 18px; margin-right: 8px; color: #f0a500; }
    </style>
    @stack('styles')
</head>
<body class="text-gray-800" style="background-color: #0a1f44;">

    <!-- NAVBAR -->
    <nav class="fixed top-0 left-0 right-0 z-50 shadow-lg" style="background: linear-gradient(to right, #ffffff 0%, #c8d8f0 10%, #4a6fa5 25%, #0a1f44 50%); height: 72px;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between" style="height:72px;">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center flex-shrink-0">
                    @php $logoPath = public_path('images/logo.png'); @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ asset('images/logo.png') }}"
                             alt="Chrisco Upper Room Fellowship"
                             style="height:52px; width:auto; object-fit:contain; display:block;">
                    @else
                        <span class="text-white text-2xl mr-3"><i class="fas fa-cross" style="color:#f0a500;"></i></span>
                        <span class="text-white font-bold text-lg leading-tight" style="font-family:'Playfair Display',serif;">
                            Chrisco Upper Room<br>
                            <span class="text-xs font-light tracking-widest" style="color:#f0a500;">WHERE GOD DWELLS</span>
                        </span>
                    @endif
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('home') }}" class="nav-link text-white px-3 py-2 text-sm font-medium">Home</a>
                    <a href="{{ route('about') }}" class="nav-link text-white px-3 py-2 text-sm font-medium">About Us</a>
                    <!-- Resources Dropdown -->
                    <div class="dropdown">
                        <button class="nav-link text-white px-3 py-2 text-sm font-medium flex items-center space-x-1 focus:outline-none">
                            <span>Resources</span>
                            <i class="fas fa-chevron-down text-xs" style="color: #f0a500;"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="{{ route('sermons.index') }}">
                                <i class="fas fa-bible"></i>Sermons
                            </a>
                            <a href="{{ route('livestream') }}">
                                <i class="fas fa-broadcast-tower"></i>Livestream
                            </a>
                            <a href="{{ route('teachings.index') }}">
                                <i class="fas fa-book-open"></i>Teachings
                            </a>
                            <a href="{{ route('resources.index') }}">
                                <i class="fas fa-file-pdf"></i>Books & Articles
                            </a>
                            <a href="{{ route('gallery.index') }}">
                                <i class="fas fa-images"></i>Gallery
                            </a>
                            <a href="{{ route('apostle.index') }}">
                                <i class="fas fa-video"></i>Apostle Das Teachings
                            </a>
                        </div>
                    </div>
                    <!-- Updates Dropdown -->
                    <div class="dropdown">
                        <button class="nav-link text-white px-3 py-2 text-sm font-medium flex items-center space-x-1 focus:outline-none">
                            <span>Updates</span>
                            <i class="fas fa-chevron-down text-xs" style="color: #f0a500;"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="{{ route('events.index') }}">
                                <i class="fas fa-calendar-alt"></i>Events
                            </a>
                            <a href="{{ route('announcements.index') }}">
                                <i class="fas fa-bullhorn"></i>Announcements
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('give') }}" class="nav-link text-white px-3 py-2 text-sm font-medium">Giving</a>
                    <a href="{{ route('prayer.index') }}" class="nav-link text-white px-3 py-2 text-sm font-medium">Prayer Request</a>
                </div>

                <!-- Auth Links (shown only when logged in) -->
                @auth
                <div class="hidden md:flex items-center gap-2 flex-shrink-0">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                           class="text-white text-sm px-3 py-1.5 rounded border border-yellow-400 hover:bg-yellow-400 hover:text-gray-900 transition leading-none flex items-center">
                            <i class="fas fa-tachometer-alt mr-1"></i>Admin
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="display:flex; align-items:center; margin:0;">
                        @csrf
                        <button type="submit" class="btn-red text-sm leading-none" style="padding:6px 16px;">Logout</button>
                    </form>
                </div>
                @endauth

                <!-- Hamburger Button -->
                <button class="md:hidden text-white focus:outline-none" onclick="toggleNavMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden" style="background-color: #0d2856;">
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="{{ route('home') }}" class="block text-white py-2 text-sm hover:text-yellow-400">Home</a>
                <a href="{{ route('about') }}" class="block text-white py-2 text-sm hover:text-yellow-400">About Us</a>
                <!-- Mobile Resources accordion -->
                <button onclick="toggleMobileMenu('resources')" class="flex items-center justify-between w-full text-white py-2 text-sm hover:text-yellow-400">
                    <span><i class="fas fa-book mr-2 text-yellow-400 text-xs"></i>Resources</span>
                    <i class="fas fa-chevron-down text-xs text-yellow-400" id="resources-chevron"></i>
                </button>
                <div id="mobile-resources-menu" class="hidden pl-4 space-y-1 pb-1">
                    <a href="{{ route('sermons.index') }}" class="block text-gray-300 py-1.5 text-sm hover:text-yellow-400">
                        <i class="fas fa-bible mr-2 text-yellow-400 text-xs"></i>Sermons
                    </a>
                    <a href="{{ route('livestream') }}" class="block text-gray-300 py-1.5 text-sm hover:text-yellow-400">
                        <i class="fas fa-broadcast-tower mr-2 text-yellow-400 text-xs"></i>Livestream
                    </a>
                    <a href="{{ route('teachings.index') }}" class="block text-gray-300 py-1.5 text-sm hover:text-yellow-400">
                        <i class="fas fa-book-open mr-2 text-yellow-400 text-xs"></i>Teachings
                    </a>
                    <a href="{{ route('resources.index') }}" class="block text-gray-300 py-1.5 text-sm hover:text-yellow-400">
                        <i class="fas fa-file-pdf mr-2 text-yellow-400 text-xs"></i>Books & Articles
                    </a>
                    <a href="{{ route('gallery.index') }}" class="block text-gray-300 py-1.5 text-sm hover:text-yellow-400">
                        <i class="fas fa-images mr-2 text-yellow-400 text-xs"></i>Gallery
                    </a>
                    <a href="{{ route('apostle.index') }}" class="block text-gray-300 py-1.5 text-sm hover:text-yellow-400">
                        <i class="fas fa-video mr-2 text-yellow-400 text-xs"></i>Apostle Das Teachings
                    </a>
                </div>
                <!-- Mobile Updates accordion -->
                <button onclick="toggleMobileMenu('updates')" class="flex items-center justify-between w-full text-white py-2 text-sm hover:text-yellow-400">
                    <span><i class="fas fa-bell mr-2 text-yellow-400 text-xs"></i>Updates</span>
                    <i class="fas fa-chevron-down text-xs text-yellow-400" id="updates-chevron"></i>
                </button>
                <div id="mobile-updates-menu" class="hidden pl-4 space-y-1 pb-1">
                    <a href="{{ route('events.index') }}" class="block text-gray-300 py-1.5 text-sm hover:text-yellow-400">
                        <i class="fas fa-calendar-alt mr-2 text-yellow-400 text-xs"></i>Events
                    </a>
                    <a href="{{ route('announcements.index') }}" class="block text-gray-300 py-1.5 text-sm hover:text-yellow-400">
                        <i class="fas fa-bullhorn mr-2 text-yellow-400 text-xs"></i>Announcements
                    </a>
                </div>
                <a href="{{ route('give') }}" class="block text-white py-2 text-sm hover:text-yellow-400">
                    <i class="fas fa-hand-holding-heart mr-2 text-yellow-400 text-xs"></i>Giving
                </a>
                <a href="{{ route('prayer.index') }}" class="block text-white py-2 text-sm hover:text-yellow-400">
                    <i class="fas fa-praying-hands mr-2 text-yellow-400 text-xs"></i>Prayer Request
                </a>
                @auth
                <hr class="border-gray-600 my-2">
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="block text-white py-2 text-sm hover:text-yellow-400">Admin Panel</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-white py-2 text-sm hover:text-red-400">Logout</button>
                </form>
                @endauth
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main style="padding-top: 72px;">
        @include('partials.flash')
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer style="background-color: #0a1f44;" class="text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- About -->
            <div>
                <div class="flex items-center space-x-2 mb-3">
                    <i class="fas fa-cross text-yellow-400 text-xl"></i>
                    <span class="text-lg font-bold" style="font-family: 'Playfair Display', serif;">Chrisco Upper Room Fellowship</span>
                </div>
                <p class="text-gray-300 text-sm mb-3 italic">"Where God Dwells"</p>
                <p class="text-gray-400 text-sm">
                    <i class="fas fa-map-marker-alt mr-2 text-yellow-400"></i>Nairobi, Kenya
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-yellow-400 font-semibold mb-3 uppercase tracking-wider text-sm">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-yellow-400 transition">Home</a></li>
                    <li><a href="{{ route('sermons.index') }}" class="text-gray-300 hover:text-yellow-400 transition">Sermons</a></li>
                    <li><a href="{{ route('events.index') }}" class="text-gray-300 hover:text-yellow-400 transition">Events</a></li>
                    <li><a href="{{ route('livestream') }}" class="text-gray-300 hover:text-yellow-400 transition">Livestream</a></li>
                    <li><a href="{{ route('give') }}" class="text-gray-300 hover:text-yellow-400 transition">Give</a></li>
                    <li><a href="{{ route('prayer.index') }}" class="text-gray-300 hover:text-yellow-400 transition">Prayer</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="text-yellow-400 font-semibold mb-3 uppercase tracking-wider text-sm">Contact Us</h4>
                <ul class="space-y-2 text-sm text-gray-300">
                    <li><i class="fas fa-phone mr-2 text-yellow-400"></i>0726900700</li>
                    <li><i class="fas fa-envelope mr-2 text-yellow-400"></i>info@chrisco-upper-room.org</li>
                    <li><i class="fas fa-map-marker-alt mr-2 text-yellow-400"></i>Nairobi, Kenya</li>
                    <li class="pt-2 flex space-x-3">
                        <a href="#" class="text-gray-300 hover:text-yellow-400"><i class="fab fa-facebook text-xl"></i></a>
                        <a href="#" class="text-gray-300 hover:text-yellow-400"><i class="fab fa-youtube text-xl"></i></a>
                        <a href="#" class="text-gray-300 hover:text-yellow-400"><i class="fab fa-twitter text-xl"></i></a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 py-4 text-center text-gray-400 text-xs">
            &copy; {{ date('Y') }} Chrisco Upper Room Fellowship. All rights reserved.
        </div>
    </footer>

    <script>
        function toggleNavMenu() {
            document.getElementById('mobile-menu').classList.toggle('open');
        }
        function toggleMobileMenu(section) {
            const menu = document.getElementById('mobile-' + section + '-menu');
            const chevron = document.getElementById(section + '-chevron');
            menu.classList.toggle('hidden');
            chevron.classList.toggle('fa-chevron-down');
            chevron.classList.toggle('fa-chevron-up');
        }
    </script>

    @stack('scripts')
</body>
</html>



