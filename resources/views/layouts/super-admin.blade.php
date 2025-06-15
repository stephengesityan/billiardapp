<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Super Admin Dashboard</title>
    <!-- Tailwind CSS via CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
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
    sidebarOpen: getSuperAdminSidebarState(), 
    userDropdownOpen: false,
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        saveSuperAdminSidebarState(this.sidebarOpen);
    }
}" x-init="
    // Watch for sidebar changes and save to localStorage
    $watch('sidebarOpen', value => saveSuperAdminSidebarState(value))
" class="bg-gray-50">

    <script>
        // Function to get sidebar state from localStorage (separate key for super admin)
        function getSuperAdminSidebarState() {
            const saved = localStorage.getItem('superadmin_sidebar_open');
            // Default to true for desktop, false for mobile
            if (saved === null) {
                return window.innerWidth >= 1024; // lg breakpoint
            }
            return saved === 'true';
        }

        // Function to save sidebar state to localStorage (separate key for super admin)
        function saveSuperAdminSidebarState(isOpen) {
            localStorage.setItem('superadmin_sidebar_open', isOpen.toString());
        }

        // Handle responsive behavior on window resize
        window.addEventListener('resize', function () {
            // Only auto-adjust if no explicit state has been saved
            const saved = localStorage.getItem('superadmin_sidebar_open');
            if (saved === null) {
                // Auto close on mobile, open on desktop
                const shouldOpen = window.innerWidth >= 1024;
                Alpine.store('sidebar', { open: shouldOpen });
            }
        });
    </script>

    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Menu Button - Always visible on mobile when sidebar is closed -->
        <div x-show="!sidebarOpen" class="fixed top-4 left-4 z-50 lg:hidden">
            <button @click="toggleSidebar()"
                class="p-2 bg-white rounded-lg shadow-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 border border-gray-200">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Sidebar Overlay -->
        <div x-show="sidebarOpen" @click="toggleSidebar()" class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden">
        </div>

        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0 lg:w-20'"
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-lg transition-all duration-300 transform lg:relative lg:translate-x-0">

            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-4 border-b">
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-lg text-gray-800" x-show="sidebarOpen">Super Admin</span>
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
                    <a href="{{ route('superadmin.dashboard') }}"
                        class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line w-5 h-5 mr-2 text-sm"></i>
                        <span x-show="sidebarOpen">Dashboard</span>
                    </a>

                    <a href="{{ route('superadmin.venue.index') }}"
                        class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('superadmin.venue.*') ? 'active' : '' }}">
                        <i class="fas fa-building w-5 h-5 mr-2 text-sm"></i>
                        <span x-show="sidebarOpen">Manajemen Venue</span>
                    </a>

                    <a href="{{ route('superadmin.admin.index') }}"
                        class="nav-item flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('superadmin.admin.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog w-5 h-5 mr-2 text-sm"></i>
                        <span x-show="sidebarOpen">Manajemen Admin</span>
                    </a>
                </nav>
            </div>

            <!-- User Profile -->
            <div class="absolute bottom-0 w-full border-t border-gray-200">
                <div x-data="{ open: false }" class="relative p-4">
                    <button @click="open = !open" class="flex items-center w-full text-left focus:outline-none">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr(auth()->user()->name ?? 'SA', 0, 1) }}
                            </div>
                        </div>
                        <div x-show="sidebarOpen" class="ml-3">
                            <p class="text-sm font-medium text-gray-800 truncate">
                                {{ auth()->user()->name ?? 'Super Admin' }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ??
                                'superadmin@example.com' }}</p>
                        </div>
                        <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg"
                            class="ml-auto h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" @click.outside="open = false"
                        class="absolute bottom-full left-0 mb-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-1 dropdown-transition">
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
            <!-- Top Header with Mobile Menu Button Alternative -->
            <header class="bg-white shadow-sm lg:hidden">
                <div class="px-4 py-3 flex items-center justify-between">
                    <button @click="toggleSidebar()" x-show="!sidebarOpen"
                        class="p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <h1 class="text-lg font-semibold text-gray-900">Super Admin</h1>

                    <!-- Mobile Profile -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center p-1 rounded-full hover:bg-gray-100 transition-colors">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                                {{ substr(auth()->user()->name ?? 'SA', 0, 1) }}
                            </div>
                        </button>

                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'Super Admin' }}
                                </p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'superadmin@example.com' }}
                                </p>
                            </div>
                            <div class="pt-2">
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