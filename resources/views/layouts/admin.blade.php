<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .nav-item {
            transition: all 0.3s ease;
        }

        .nav-item.active {
            position: relative;
            background-color: rgb(239, 246, 255);
            color: rgb(37, 99, 235);
            font-weight: 500;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: rgb(37, 99, 235);
            border-radius: 0 4px 4px 0;
        }

        .nav-item:hover:not(.active) {
            background-color: rgb(249, 250, 251);
            color: rgb(55, 65, 81);
        }

        .dropdown-transition {
            transition: all 0.2s ease-out;
        }
    </style>
</head>

<body x-data="{ 
    sidebarOpen: getSidebarState(), 
    userDropdownOpen: false,
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        saveSidebarState(this.sidebarOpen);
    }
}" x-init="
    // Watch for sidebar changes and save to localStorage
    $watch('sidebarOpen', value => saveSidebarState(value))
" class="bg-gray-50">

    <script>
        // Function to get sidebar state from localStorage
        function getSidebarState() {
            const saved = localStorage.getItem('admin_sidebar_open');
            // Default to true for desktop, false for mobile
            if (saved === null) {
                return window.innerWidth >= 1024; // lg breakpoint
            }
            return saved === 'true';
        }

        // Function to save sidebar state to localStorage
        function saveSidebarState(isOpen) {
            localStorage.setItem('admin_sidebar_open', isOpen.toString());
        }

        // Handle responsive behavior on window resize
        window.addEventListener('resize', function () {
            // Only auto-adjust if no explicit state has been saved
            const saved = localStorage.getItem('admin_sidebar_open');
            if (saved === null) {
                // Auto close on mobile, open on desktop
                const shouldOpen = window.innerWidth >= 1024;
                Alpine.store('sidebar', { open: shouldOpen });
            }
        });
    </script>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Overlay -->
        <div x-show="sidebarOpen" @click="toggleSidebar()" class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden">
        </div>

        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0 lg:w-20'"
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-lg transition-all duration-300 transform lg:relative lg:translate-x-0">

            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-4 border-b">
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-lg text-gray-800" x-show="sidebarOpen">Admin</span>
                    {{-- <div class="p-3 rounded-lg">
                        <a href="/">
                            <img src="{{ asset('images/carimeja3.png') }}" alt="carimeja.com" class="w-24">
                        </a>
                    </div> --}}
                </div>
                <button @click="toggleSidebar()" class="p-1 rounded-md hover:bg-gray-100 focus:outline-none">
                    <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                    <svg x-show="!sidebarOpen" class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <div class="px-2 py-4">
                <div x-show="sidebarOpen"
                    class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">
                    Menu Utama
                </div>
                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h7v7H3V3zm11 0h7v7h-7V3zM3 14h7v7H3v-7zm11 0h7v7h-7v-7z" />
                        </svg>
                        <span x-show="sidebarOpen">Dashboard</span>
                    </a>

                    <a href="{{ route('admin.venue.index') }}"
                        class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.venue.*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10l9-7 9 7v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V14H9v6a2 2 0 01-2 2H5a2 2 0 01-2-2V10z" />
                        </svg>
                        <span x-show="sidebarOpen">Kelola Venue</span>
                    </a>

                    <a href="{{ route('admin.tables.index') }}"
                        class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.tables.*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <rect width="18" height="10" x="3" y="7" rx="2" ry="2" stroke-width="2"
                                stroke="currentColor" fill="none" />
                            <circle cx="12" cy="12" r="1.5" fill="currentColor" />
                        </svg>
                        <span x-show="sidebarOpen">Kelola Meja</span>
                    </a>

                    <a href="{{ route('admin.bookings.index') }}"
                        class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                        </svg>
                        <span x-show="sidebarOpen">Kelola Booking</span>
                    </a>
                    <a href="{{ route('admin.revenues.index') }}"
                        class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.revenues.*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.5 0-3 .75-3 2s1.5 2 3 2 3 .75 3 2-1.5 2-3 2m0-10v10m-6 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span x-show="sidebarOpen">Laporan Pendapatan</span>
                    </a>
                </nav>

                {{-- <div x-show="sidebarOpen"
                    class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2 mt-6">
                    Sistem
                </div>
                <nav class="space-y-1">
                    <a href="#" class="nav-item flex items-center px-3 py-2.5 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span x-show="sidebarOpen">Notifikasi</span>
                        <span class="ml-auto bg-red-500 text-white px-2 py-0.5 rounded-full text-xs"
                            x-show="sidebarOpen">3</span>
                    </a>

                    <a href="#" class="nav-item flex items-center px-3 py-2.5 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span x-show="sidebarOpen">Pengaturan</span>
                    </a>
                </nav> --}}
            </div>

            <!-- User Profile -->
            <div class="absolute bottom-0 w-full border-t border-gray-200">
                <div x-data="{ open: false }" class="relative p-4">
                    <button @click="open = !open" class="flex items-center w-full text-left focus:outline-none">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div x-show="sidebarOpen" class="ml-3">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg"
                            class="ml-auto h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" @click.outside="open = false"
                        class="absolute bottom-full left-0 mb-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-1 dropdown-transition">
                        <a href="{{ route('admin.profile.index') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                        {{-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                        --}}
                        <div class="border-t border-gray-200 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm lg:hidden">
                <div class="px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <button @click="toggleSidebar()"
                        class="p-1 rounded-md text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 lg:hidden">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="lg:hidden">
                        <span class="font-semibold text-lg">{{ config('app.name') }}</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>