<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite('resources/css/app.css')
    {{-- Font | Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    {{-- Icon | Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="font-poppins">
    <header class="fixed top-0 w-full bg-white shadow-sm z-50" x-data="{ showModal: false, modalType: '' }">
        <nav x-data="{ isMobileMenuOpen: false }" class="relative py-4 px-4 lg:px-44 flex items-center justify-between">
            <a href="https://ayo.co.id">
                <img src="{{ asset('images/ayo.png') }}" alt="Ayo Logo" class="w-24">
            </a>

            <div class="flex items-center space-x-4">
                <a href="#"><i class="fa fa-shopping-cart text-xl text-gray-700"></i></a>

                <!-- Mobile hamburger -->
                <button @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="block lg:hidden border-l pl-4 border-gray-300 focus:outline-none">
                    <i class="fas fa-bars text-2xl text-gray-700"></i>
                </button>

                <!-- Desktop buttons -->
                <div class="hidden lg:flex items-center space-x-4">
                    <button @click="showModal = true; modalType = 'login'"
                        class="text-sm font-medium text-gray-700 hover:text-primary transition">Masuk</button>
                    <button @click="showModal = true; modalType = 'register'"
                        class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded text-sm font-medium transition">Daftar</button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-show="isMobileMenuOpen" @click.away="isMobileMenuOpen = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-4"
                class="absolute top-full left-0 w-full bg-white shadow-lg mt-2 px-6 py-4 space-y-3 lg:hidden z-50 rounded-b-xl">
                <button @click="showModal = true; modalType = 'login'"
                    class="block w-full text-left text-sm font-medium text-gray-700 hover:text-primary transition">Masuk</button>
                <button @click="showModal = true; modalType = 'register'"
                    class="block w-full text-left bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded text-sm font-medium transition">Daftar</button>
            </div>
        </nav>



        <!-- Modal -->
        <div x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

            <!-- Modal Box -->
            <div @click.away="showModal = false" class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">

                <button @click="showModal = false"
                    class="absolute top-3 right-4 text-gray-500 hover:text-gray-700 text-xl">
                    &times;
                </button>

                <template x-if="modalType === 'login'">
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Masuk</h2>
                        <form method="POST" action="{{ route('login') }}" class="space-y-4">
                            @csrf
                            <input type="email" name="email" placeholder="Email" class="w-full border px-4 py-2 rounded"
                                required>
                            <input type="password" name="password" placeholder="Password"
                                class="w-full border px-4 py-2 rounded" required>
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">Masuk</button>
                        </form>
                        <p class="text-sm mt-4 text-center">
                            Belum punya akun?
                            <button @click="modalType = 'register'" class="text-primary hover:underline">Daftar</button>
                        </p>
                    </div>
                </template>

                <template x-if="modalType === 'register'">
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Daftar</h2>
                        <form method="POST" action="{{ route('register') }}" class="space-y-4">
                            @csrf
                            <input type="text" name="name" placeholder="Nama Lengkap"
                                class="w-full border px-4 py-2 rounded" required>
                            <input type="email" name="email" placeholder="Email" class="w-full border px-4 py-2 rounded"
                                required>
                            <input type="password" name="password" placeholder="Password"
                                class="w-full border px-4 py-2 rounded" required>
                            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                                class="w-full border px-4 py-2 rounded" required>
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">Daftar</button>
                        </form>
                        <p class="text-sm mt-4 text-center">
                            Sudah punya akun?
                            <button @click="modalType = 'login'" class="text-primary hover:underline">Masuk</button>
                        </p>
                    </div>
                </template>
            </div>
        </div>
    </header>


    <main class="pt-20">
        @yield('content')
        @if(Auth::check())
            <p>Halo, {{ Auth::user()->name }}</p>
        @else
            <p>Kamu belum login</p>
        @endif
    </main>

    <footer class="bg-white text-gray-400 relative w-full pt-20">
        <div class="container mx-auto px-4 absolute bottom-5">
            <div class="text-center">
                <p class="text-sm">&copy; 2025 Ayo Venue. All rights reserved.</p>
            </div>
            <div class="text-center">
                <a href="#" class="text-sm text-gray-400">Privacy Policy</a> |
                <a href="#" class="text-sm text-gray-400">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>

</html>