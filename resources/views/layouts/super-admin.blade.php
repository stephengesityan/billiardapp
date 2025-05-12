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
</head>

<body class="bg-gray-100">
    <div x-data="{ sidebarOpen: true }">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform bg-blue-800 text-white">
            <div class="flex items-center justify-between p-4 border-b border-blue-700">
                <h2 class="text-2xl font-bold">Venue System</h2>
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">Super Admin</p>
                            <p class="text-xs opacity-75">{{ auth()->user()->name ?? 'Admin' }}</p>
                        </div>
                    </div>
                </div>
                <nav>
                    <ul>
                        <li class="mb-2">
                            <a href="{{ route('superadmin.dashboard') }}"
                                class="flex items-center p-3 rounded-lg hover:bg-blue-700 {{ request()->routeIs('superadmin.dashboard') ? 'bg-blue-700' : '' }}">
                                <i class="fas fa-tachometer-alt w-5"></i>
                                <span class="ml-3">Dashboard</span>
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="{{ route('superadmin.venue.index') }}"
                                class="flex items-center p-3 rounded-lg hover:bg-blue-700 {{ request()->routeIs('superadmin.venue.*') ? 'bg-blue-700' : '' }}">
                                <i class="fas fa-building w-5"></i>
                                <span class="ml-3">Manajemen Venue</span>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('superadmin.admin.index') }}"
                                class="flex items-center p-3 rounded-lg hover:bg-blue-700 {{ request()->routeIs('superadmin.admin.*') ? 'bg-blue-700' : '' }}">
                                <i class="fas fa-users-cog w-5"></i>
                                <span class="ml-3">Manajemen Admin</span>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="flex items-center p-3 rounded-lg hover:bg-blue-700">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                <span class="ml-3">Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content -->
        <div :class="sidebarOpen ? 'lg:ml-64' : ''" class="transition-all duration-300">
            <!-- Top bar -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
                <div class="px-4 py-3 flex items-center justify-between">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 focus:outline-none">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="flex items-center space-x-4">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-600 focus:outline-none">
                                <span class="mr-2">{{ auth()->user()->name ?? 'Admin' }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
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