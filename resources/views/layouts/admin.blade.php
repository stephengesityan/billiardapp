<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body x-data="{ sidebarOpen: true }" class="flex">

    <!-- Sidebar -->
    <div :class="sidebarOpen ? 'w-64' : 'w-16'" class="bg-white border-r h-screen transition-all duration-300">
        <div class="flex justify-between items-center p-4">
            <div class="flex items-center space-x-2">
                <span class="font-bold text-lg" x-show="sidebarOpen">Admin Panel</span>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 focus:outline-none">
                <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex flex-col justify-between h-full mt-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-gray-100">📊 <span
                            x-show="sidebarOpen">Dashboard</span></a></li>
                <li><a href="{{ route('admin.tables.index') }}" class="block px-4 py-2 hover:bg-gray-100">🪑 <span
                            x-show="sidebarOpen">Kelola
                            Meja</span></a></li>
                <li><a href="{{ route('admin.bookings.index') }}" class="block px-4 py-2 hover:bg-gray-100">📅 <span
                            x-show="sidebarOpen">Daftar
                            Booking</span></a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">👥 <span x-show="sidebarOpen">Data
                            User</span></a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">🔔 <span
                            x-show="sidebarOpen">Notifikasi</span></a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">⚙️ <span
                            x-show="sidebarOpen">Pengaturan</span></a></li>
                <li>
                    <div class="relative mt-4 px-4" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center w-full text-left">
                            <span class="truncate" x-show="sidebarOpen">{{ auth()->user()->name }}</span>
                            <svg x-show="sidebarOpen" class="ml-1 h-4 w-4" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.outside="open = false"
                            class="absolute left-4 mt-2 w-48 bg-white border rounded shadow-md z-10" x-cloak>
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">Edit Profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</button>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- User Dropdown -->
    </div>

    <!-- Main content -->
    <div class="flex-1 p-6 bg-gray-50 min-h-screen">
        @yield('content')
    </div>

</body>

</html>