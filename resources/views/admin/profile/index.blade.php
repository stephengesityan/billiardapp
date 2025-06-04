@extends('layouts.admin')

@section('content')
    <div class="p-6">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Pengaturan Profile</h1>
            <p class="text-gray-600 mt-1">Kelola informasi akun dan keamanan Anda</p>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Profile Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Informasi Profile</h2>
                    <p class="text-sm text-gray-600 mt-1">Perbarui informasi dasar akun Anda</p>
                </div>

                <form action="{{ route('admin.profile.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <!-- Profile Avatar -->
                    <div class="flex items-center space-x-4 mb-6">
                        <div
                            class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ ucfirst($user->role) }}</p>
                            @if($user->venue)
                                <p class="text-sm text-blue-600">{{ $user->venue->name }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Name Field -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                            readonly>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Account Info -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Informasi Akun</h4>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><span class="font-medium">Role:</span> {{ ucfirst($user->role) }}</p>
                            <p><span class="font-medium">Bergabung:</span> {{ $user->created_at->format('d M Y') }}</p>
                            @if($user->email_verified_at)
                                <p><span class="font-medium">Status Email:</span>
                                    <span class="text-green-600">✓ Terverifikasi</span>
                                </p>
                            @else
                                <p><span class="font-medium">Status Email:</span>
                                    <span class="text-red-600">✗ Belum Terverifikasi</span>
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                        Perbarui Profile
                    </button>
                </form>
            </div>

            <!-- Change Password Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Ubah Password</h2>
                    <p class="text-sm text-gray-600 mt-1">Pastikan akun Anda menggunakan password yang kuat</p>
                </div>

                <form action="{{ route('admin.profile.password') }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div class="mb-4">
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Saat Ini
                        </label>
                        <input type="password" id="current_password" name="current_password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('current_password') border-red-500 @enderror">
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Baru
                        </label>
                        <input type="password" id="password" name="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password Baru
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Password Requirements -->
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="font-medium text-yellow-800 mb-2">Persyaratan Password:</h4>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• Minimal 8 karakter</li>
                            <li>• Kombinasi huruf besar dan kecil</li>
                            <li>• Minimal satu angka</li>
                            <li>• Minimal satu karakter khusus</li>
                        </ul>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                        Ubah Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="font-medium text-blue-900">Tips Keamanan</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Selalu gunakan password yang unik dan kuat. Jangan bagikan informasi login Anda kepada siapa pun.
                        Jika Anda mencurigai adanya aktivitas yang tidak biasa, segera ubah password Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection