<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Super Admin Dashboard</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.12.0/cdn.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Custom scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }
        
        /* Hover effects */
        .nav-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }
        
        .nav-item:hover::before {
            left: 100%;
        }
        
        /* Active nav indicator */
        .nav-active {
            background: rgba(255, 255, 255, 0.15);
            border-right: 3px solid white;
        }
        
        /* Profile section gradient */
        .profile-section {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="bg-gray-50">
    <div x-data="{ sidebarOpen: true }">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed top-0 left-0 z-40 w-72 h-screen transition-transform duration-300 ease-in-out bg-gradient-to-br from-slate-800 via-slate-700 to-slate-900 text-white shadow-2xl">
            
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-slate-600/30">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-cubes text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                            VenueSystem
                        </h2>
                        <p class="text-xs text-slate-400 font-medium">Super Admin Panel</p>
                    </div>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" 
                    class="lg:hidden w-8 h-8 rounded-lg bg-slate-600/50 hover:bg-slate-600 transition-colors flex items-center justify-center">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            
            <!-- Profile Section -->
            <div class="p-6">
                <div class="profile-section rounded-xl p-4 mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-cyan-500 flex items-center justify-center shadow-lg">
                                <i class="fas fa-user-shield text-white text-lg"></i>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-slate-800"></div>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-white">{{ auth()->user()->name ?? 'Admin' }}</p>
                            <p class="text-sm text-slate-300">Super Administrator</p>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1"></span>
                                    Online
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav class="space-y-2">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 px-3">
                        Main Navigation
                    </div>
                    
                    <a href="{{ route('superadmin.dashboard') }}"
                        class="nav-item flex items-center p-3 rounded-lg hover:bg-slate-600/30 {{ request()->routeIs('superadmin.dashboard') ? 'nav-active' : '' }} group">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500/20 to-blue-600/20 flex items-center justify-center group-hover:from-blue-500/30 group-hover:to-blue-600/30 transition-all">
                            <i class="fas fa-chart-line text-blue-400 group-hover:text-blue-300"></i>
                        </div>
                        <div class="ml-4">
                            <span class="font-medium text-slate-200 group-hover:text-white transition-colors">Dashboard</span>
                            <p class="text-xs text-slate-400 group-hover:text-slate-300">Analytics & Overview</p>
                        </div>
                    </a>

                    <a href="{{ route('superadmin.venue.index') }}"
                        class="nav-item flex items-center p-3 rounded-lg hover:bg-slate-600/30 {{ request()->routeIs('superadmin.venue.*') ? 'nav-active' : '' }} group">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 flex items-center justify-center group-hover:from-emerald-500/30 group-hover:to-emerald-600/30 transition-all">
                            <i class="fas fa-building text-emerald-400 group-hover:text-emerald-300"></i>
                        </div>
                        <div class="ml-4">
                            <span class="font-medium text-slate-200 group-hover:text-white transition-colors">Manajemen Venue</span>
                            <p class="text-xs text-slate-400 group-hover:text-slate-300">Kelola semua venue</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('superadmin.admin.index') }}"
                        class="nav-item flex items-center p-3 rounded-lg hover:bg-slate-600/30 {{ request()->routeIs('superadmin.admin.*') ? 'nav-active' : '' }} group">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500/20 to-purple-600/20 flex items-center justify-center group-hover:from-purple-500/30 group-hover:to-purple-600/30 transition-all">
                            <i class="fas fa-users-cog text-purple-400 group-hover:text-purple-300"></i>
                        </div>
                        <div class="ml-4">
                            <span class="font-medium text-slate-200 group-hover:text-white transition-colors">Manajemen Admin</span>
                            <p class="text-xs text-slate-400 group-hover:text-slate-300">Kelola admin venue</p>
                        </div>
                    </a>

                    <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="flex items-center p-3 rounded-lg hover:bg-red-700">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                <span class="ml-3">Logout</span>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </a>
                </nav>
            </div>
        </aside>

        <!-- Content -->
        <div :class="sidebarOpen ? 'lg:ml-72' : ''" class="transition-all duration-300">
            <!-- Top bar -->
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-200/50 sticky top-0 z-30 shadow-sm">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" 
                            class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors flex items-center justify-center text-gray-600 hover:text-gray-800">
                            <i class="fas fa-bars text-sm"></i>
                        </button>
                        
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-sm text-gray-500">
                            <span class="font-medium text-gray-900">Super Admin</span>
                            <i class="fas fa-chevron-right text-xs"></i>
                            <span>Dashboard</span>
                        </nav>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors flex items-center justify-center text-gray-600 hover:text-gray-800">
                            <i class="fas fa-bell text-sm"></i>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
                        </button>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-cyan-500 flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <div class="text-left hidden md:block">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'Admin' }}</p>
                                    <p class="text-xs text-gray-500">Super Admin</p>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-400" 
                                   :class="{ 'rotate-180': open }" 
                                   style="transition: transform 0.2s"></i>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'Admin' }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
                                </div>
                                {{-- <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-user mr-3 text-gray-400"></i>
                                    Profile Settings
                                </a>
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-cog mr-3 text-gray-400"></i>
                                    Account Settings
                                </a> --}}
                                <div class="border-t border-gray-100 mt-2 pt-2">
                                    <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>