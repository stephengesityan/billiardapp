@extends('layouts.main')

@section('content')
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="container mx-auto px-4 lg:px-44">
            <div class="flex justify-center">
                <div class="w-full max-w-2xl">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white flex items-center">
                                <i class="fas fa-user-cog mr-2"></i>{{ __('Pengaturan Akun') }}
                            </h2>
                        </div>
                        <div class="p-6">

                            @if (session('message') || session('success'))
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md flex items-center"
                                    role="alert">
                                    <div class="mr-2">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        {{ session('message') ?? session('success') }}
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('account.update') }}" class="space-y-6">
                                @csrf
                                @method('PUT')

                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-user text-blue-600 mr-1"></i>{{ __('Nama') }}
                                    </label>
                                    <div class="flex rounded-md shadow-sm">
                                        <span
                                            class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                            <i class="fas fa-user-edit"></i>
                                        </span>
                                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                                            required autofocus
                                            class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none rounded-r-md border-gray-300 p-1"
                                            placeholder="Masukkan nama">
                                    </div>
                                    @error('name')
                                        <div class="text-red-500 mt-1 text-sm"><i
                                                class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-envelope text-blue-600 mr-1"></i>{{ __('Email') }}
                                    </label>
                                    <div class="flex rounded-md shadow-sm">
                                        <span
                                            class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                            <i class="fas fa-at"></i>
                                        </span>
                                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                                            required
                                            class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none rounded-r-md border-gray-300 p-1"
                                            placeholder="Masukkan email">
                                    </div>
                                    @error('email')
                                        <div class="text-red-500 mt-1 text-sm"><i
                                                class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                    @enderror
                                    @if ($user->email)
                                        <div class="mt-2 text-sm flex items-center">
                                            @if ($user->hasVerifiedEmail())
                                                <span class="text-green-600 flex items-center">
                                                    <i class="fas fa-check-circle mr-1"></i> {{ __('Email terverifikasi') }}
                                                </span>
                                            @else
                                                <span class="text-yellow-600 flex items-center">
                                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ __('Belum Terverifikasi') }}
                                                </span>
                                                <a href="{{ route('verification.resend') }}"
                                                    class="ml-2 text-blue-600 hover:underline">
                                                    {{ __('Kirim ulang email verifikasi') }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="bg-gray-50 rounded-lg p-4 mt-6 mb-6">
                                    <h3 class="text-lg font-semibold text-blue-600 mb-4 flex items-center">
                                        <i class="fas fa-lock mr-2"></i>{{ __('Ubah Password') }}
                                    </h3>

                                    <!-- Current Password -->
                                    <div class="mb-4">
                                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-key text-blue-600 mr-1"></i>{{ __('Password Saat Ini') }}
                                        </label>
                                        <div class="flex rounded-md shadow-sm">
                                            <span
                                                class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input id="current_password" type="password" name="current_password"
                                                class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none border-gray-300 p-1"
                                                placeholder="Masukkan password saat ini">
                                            <button type="button" id="toggleCurrentPassword"
                                                class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 hover:bg-gray-100">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('current_password')
                                            <div class="text-red-500 mt-1 text-sm"><i
                                                    class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- New Password -->
                                    <div class="mb-4">
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-lock-open text-blue-600 mr-1"></i>{{ __('Password Baru') }}
                                        </label>
                                        <div class="flex rounded-md shadow-sm">
                                            <span
                                                class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                                <i class="fas fa-key"></i>
                                            </span>
                                            <input id="password" type="password" name="password"
                                                class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none border-gray-300 p-1"
                                                placeholder="Masukkan password baru">
                                            <button type="button" id="toggleNewPassword"
                                                class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 hover:bg-gray-100">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="text-red-500 mt-1 text-sm"><i
                                                    class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Confirm New Password -->
                                    <div>
                                        <label for="password_confirmation"
                                            class="block text-sm font-medium text-gray-700 mb-1">
                                            <i
                                                class="fas fa-check-double text-blue-600 mr-1"></i>{{ __('Konfirmasi Password Baru') }}
                                        </label>
                                        <div class="flex rounded-md shadow-sm">
                                            <span
                                                class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                                <i class="fas fa-key"></i>
                                            </span>
                                            <input id="password_confirmation" type="password" name="password_confirmation"
                                                class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none border-gray-300 p-1"
                                                placeholder="Konfirmasi password baru">
                                            <button type="button" id="toggleConfirmPassword"
                                                class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 hover:bg-gray-100">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end mt-6">
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center transition">
                                        <i class="fas fa-save mr-2"></i>{{ __('Simpan Perubahan') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.addEventListener('DOMContentLoaded', function () {
            const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
            const toggleNewPassword = document.getElementById('toggleNewPassword');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

            const currentPassword = document.getElementById('current_password');
            const newPassword = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');

            if (toggleCurrentPassword) {
                toggleCurrentPassword.addEventListener('click', function () {
                    togglePasswordVisibility(currentPassword, this);
                });
            }

            if (toggleNewPassword) {
                toggleNewPassword.addEventListener('click', function () {
                    togglePasswordVisibility(newPassword, this);
                });
            }

            if (toggleConfirmPassword) {
                toggleConfirmPassword.addEventListener('click', function () {
                    togglePasswordVisibility(confirmPassword, this);
                });
            }

            function togglePasswordVisibility(input, button) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);

                // Toggle icon
                const icon = button.querySelector('i');
                if (type === 'text') {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    </script>
@endsection