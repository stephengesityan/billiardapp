@extends('layouts.super-admin')

@section('content')
    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="w-full max-w-2xl">
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden"
                style="backdrop-filter: blur(20px); background-color: rgba(255, 255, 255, 0.8);">
                <div class="p-6 sm:p-10">
                    <h2 class="text-center text-4xl font-semibold text-gray-900 mb-8">
                        {{ __('Tambah Admin Baru') }}
                    </h2>

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                            <ul class="space-y-1 text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li class="flex items-center">
                                        <svg class="h-4 w-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-11.293a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 101.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('superadmin.admin.store') }}" method="POST" x-data="{ 
                                showPassword: false, 
                                showConfirmPassword: false,
                                togglePassword() { 
                                    this.showPassword = !this.showPassword 
                                },
                                toggleConfirmPassword() { 
                                    this.showConfirmPassword = !this.showConfirmPassword 
                                }
                            }" class="space-y-6">
                        @csrf

                        <div class="grid md:grid-cols-2 gap-6">
                            {{-- Nama Admin --}}
                            <div class="col-span-2 md:col-span-1">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Nama Admin') }}
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    autocomplete="name" autofocus
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out"
                                    placeholder="Masukkan nama admin">
                            </div>

                            {{-- Email --}}
                            <div class="col-span-2 md:col-span-1">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Email') }}
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out"
                                    placeholder="Masukkan email admin">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            {{-- Password --}}
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Password') }}
                                </label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out"
                                        placeholder="Masukkan password">
                                    <button type="button" @click="togglePassword()"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                        <svg x-show="!showPassword" class="h-5 w-5 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                        <svg x-show="showPassword" class="h-5 w-5 text-blue-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Konfirmasi Password') }}
                                </label>
                                <div class="relative">
                                    <input :type="showConfirmPassword ? 'text' : 'password'" id="password_confirmation"
                                        name="password_confirmation" required
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out"
                                        placeholder="Konfirmasi password">
                                    <button type="button" @click="toggleConfirmPassword()"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                        <svg x-show="!showConfirmPassword" class="h-5 w-5 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                        <svg x-show="showConfirmPassword" class="h-5 w-5 text-blue-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            {{-- Venue --}}
                            <div>
                                <label for="venue_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Venue') }}
                                </label>
                                <select id="venue_id" name="venue_id" required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out">
                                    <option value="">-- Pilih Venue --</option>
                                    @foreach($venues as $venue)
                                        <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>
                                            {{ $venue->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Role --}}
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Role') }}
                                </label>
                                <select id="role" name="role" required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out">
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                        Admin
                                    </option>
                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>
                                        User
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end space-x-4 pt-6">
                            <a href="{{ route('superadmin.admin.index') }}"
                                class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition duration-300 ease-in-out">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit"
                                class="px-6 py-3 bg-blue-500 text-white rounded-lg font-medium hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-300 ease-in-out">
                                {{ __('Simpan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endpush
@endsection