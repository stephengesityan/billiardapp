<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <header class="fixed top-0 w-full bg-white shadow-sm z-50" x-data="{ 
    showModal: {{ $errors->any() || session('login_error') || session('register_error') || session('verified') || session('status') || session('reset') || request()->has('token') ? 'true' : 'false' }}, 
    modalType: '{{ session('reset') || request()->has('token') ? 'reset_password' : (session('status') ? 'forgot_password' : (session('login_error') || session('verified') ? 'login' : (session('register_error') ? 'register' : ($errors->any() ? (old('email') && !old('name') && !isset($request) ? 'login' : (request()->is('password/reset*') ? 'reset_password' : 'register')) : '')))) }}' 
}">
        <nav x-data="{ isMobileMenuOpen: false }" class="relative py-4 px-4 lg:px-44 flex items-center justify-between">
            <a href="/">
                <img src="{{ asset('images/carimeja3.png') }}" alt="carimeja.com" class="w-24">
            </a>

            <div class="flex items-center space-x-4">
                <!-- Mobile hamburger -->
                <button @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="block lg:hidden border-l pl-4 border-gray-300 focus:outline-none">
                    <i class="fas fa-bars text-2xl text-gray-700"></i>
                </button>

                <!-- Desktop buttons -->
                <div class="hidden lg:flex items-center space-x-4">
                    @auth
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-primary focus:outline-none">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg py-2 z-50">
                                <a href="{{ route('booking.history') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Riwayat Booking
                                </a>
                                <a href="{{ route('account.settings') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Pengaturan Akun
                                </a>
                                @if (Auth::user()->email_verified_at === null)
                                    <a href="{{ route('verification.notice') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <span class="text-orange-500"><i class="fas fa-exclamation-circle mr-1"></i></span>
                                        Verifikasi Email
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <button @click="showModal = true; modalType = 'login'"
                            class="text-sm font-medium text-gray-700 hover:text-primary transition">Masuk</button>
                        <button @click="showModal = true; modalType = 'register'"
                            class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded text-sm font-medium transition">Daftar</button>
                    @endauth
                </div>

                <!-- Mobile menu -->
                <div x-show="isMobileMenuOpen"
                    class="absolute top-full left-0 right-0 bg-white shadow-md mt-1 p-4 z-50">
                    @auth
                        <a href="{{ route('booking.history') }}"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Riwayat Booking
                        </a>
                        <a href="{{ route('account.settings') }}"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Pengaturan Akun
                        </a>
                        @if (Auth::user()->email_verified_at === null)
                            <a href="{{ route('verification.notice') }}"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <span class="text-orange-500"><i class="fas fa-exclamation-circle mr-1"></i></span>
                                Verifikasi Email
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>
                    @else
                        <button @click="showModal = true; modalType = 'login'"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Masuk</button>
                        <button @click="showModal = true; modalType = 'register'"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 mt-2">Daftar</button>
                    @endauth
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

                <!-- Login Modal -->
                <template x-if="modalType === 'login'">
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Masuk</h2>

                        <!-- Error message for login errors -->
                        @if(session('login_error') || ($errors->any() && old('email') && !old('name')))
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                                @if(session('login_error'))
                                    <p>{{ session('login_error') }}</p>
                                    @if(str_contains(session('login_error'), 'belum diverifikasi'))
                                        <form method="POST" action="{{ route('verification.resend') }}" class="mt-2">
                                            @csrf
                                        </form>
                                    @endif
                                @else
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="space-y-4">
                            @csrf
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email"
                                class="w-full border px-4 py-2 rounded" required>
                            <input type="password" name="password" placeholder="Password"
                                class="w-full border px-4 py-2 rounded" required>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                </div>
                                <button type="button" @click="modalType = 'forgot_password'"
                                    class="text-sm text-primary hover:underline">
                                    Lupa Password?
                                </button>
                            </div>

                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">Masuk</button>
                        </form>
                        <p class="text-sm mt-4 text-center">
                            Belum punya akun?
                            <button @click="modalType = 'register'" class="text-primary hover:underline">Daftar</button>
                        </p>
                    </div>
                </template>

                <!-- Register Modal -->
                <template x-if="modalType === 'register'">
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Daftar</h2>

                        <!-- Error message for register errors -->
                        @if(session('register_error') || ($errors->any() && old('name')))
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                                @if(session('register_error'))
                                    <p>{{ session('register_error') }}</p>
                                @else
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" class="space-y-4">
                            @csrf
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Lengkap"
                                class="w-full border px-4 py-2 rounded" required>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email"
                                class="w-full border px-4 py-2 rounded" required>
                            <input type="password" name="password" placeholder="Password"
                                class="w-full border px-4 py-2 rounded" required>
                            <small class="px-4 text-gray-500">Password harus terdiri dari minimal 8 karakter.</small>
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

                <!-- Forgot Password Modal -->
                <template x-if="modalType === 'forgot_password'">
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Lupa Password</h2>

                        <!-- Success message -->
                        @if(session('status'))
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                                <p>{{ session('status') }}</p>
                            </div>
                        @endif

                        <!-- Error messages -->
                        @if($errors->has('email'))
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                                <ul>
                                    @foreach($errors->get('email') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <p class="text-sm mb-4">Masukkan alamat email Anda dan kami akan mengirimkan link untuk atur
                            ulang password.</p>

                        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                            @csrf
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email"
                                class="w-full border px-4 py-2 rounded" required>

                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">Kirim Link Reset
                                Password</button>
                        </form>

                        <p class="text-sm mt-4 text-center">
                            <button @click="modalType = 'login'" class="text-primary hover:underline">Kembali ke Halaman
                                Login</button>
                        </p>
                    </div>
                </template>

                <!-- Reset Password Modal -->
                <template x-if="modalType === 'reset_password'">
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Reset Password</h2>

                        <!-- Error messages -->
                        @if($errors->any())
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="token"
                                value="{{ request()->query('token') ?? request()->input('token') ?? old('token') }}">

                            <input type="email" name="email" value="{{ request()->query('email') ?? old('email') }}"
                                placeholder="Email" class="w-full border px-4 py-2 rounded" required>

                            <input type="password" name="password" placeholder="Password Baru"
                                class="w-full border px-4 py-2 rounded" required>
                            <small class="px-4 text-gray-500">Password harus terdiri dari minimal 8 karakter.</small>

                            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password Baru"
                                class="w-full border px-4 py-2 rounded" required>

                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">Reset
                                Password</button>
                        </form>
                    </div>
                </template>
            </div>
        </div>
    </header>

    <main class="pt-20">
        @if (session('success') || session('error') || session('verified'))
            <div id="floating-alert"
                style="position: fixed;top: 30px;left: 50%;transform: translateX(-50%);background-color: {{ session('success') || session('verified') ? '#d1e7dd' : '#f8d7da' }};color: {{ session('success') || session('verified') ? '#0f5132' : '#842029' }};padding: 10px 20px;border-radius: 6px;font-size: 14px;font-weight: 500;box-shadow: 0 3px 10px rgba(0,0,0,0.15);z-index: 9999;max-width: 300px;text-align: center">
                {{ session('success') ?? session('error') ?? session('verified') }}
            </div>

            <script>
                setTimeout(() => {
                    const alert = document.getElementById('floating-alert');
                    if (alert) {
                        alert.style.transition = 'opacity 0.5s ease';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 500);
                    }
                }, 3000);

                // Jika ada pesan verified, buka modal login
                @if(session('verified'))
                    document.addEventListener('DOMContentLoaded', function () {
                        const alpineData = document.querySelector('header').__x.$data;
                        alpineData.showModal = true;
                        alpineData.modalType = 'login';
                    });
                @endif
            </script>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white text-gray-400 relative w-full pt-20">
        <div class="container mx-auto px-4 absolute bottom-5">
            <div class="text-center">
                <p class="text-sm">&copy; 2025 Cari Meja. All rights reserved.</p>
            </div>
            <div class="text-center">
                <a href="#" class="text-sm text-gray-400">Privacy Policy</a> |
                <a href="#" class="text-sm text-gray-400">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>

<script>
    // Function to get URL parameters
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // When the page loads, check for token and email in URL parameters
    document.addEventListener('DOMContentLoaded', function () {
        const token = getUrlParameter('token');
        const email = getUrlParameter('email');

        if (token && email) {
            // Set the form values
            setTimeout(() => {
                const tokenInput = document.querySelector('input[name="token"]');
                const emailInput = document.querySelector('input[name="email"]');

                if (tokenInput) tokenInput.value = token;
                if (emailInput) emailInput.value = email;

                // Open the reset password modal
                const alpineData = document.querySelector('header').__x.$data;
                alpineData.showModal = true;
                alpineData.modalType = 'reset_password';
            }, 100);
        }
    });
</script>

</html>