@extends('layouts.super-admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Super Admin</h1>
        <p class="text-gray-600">Selamat datang di panel kontrol Super Admin</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-users-cog mr-2 text-blue-600"></i>Admin
                </h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">
                    Total: {{ $adminCount ?? 0 }}
                </span>
            </div>
            <p class="text-gray-600 mb-4">Kelola semua admin venue dalam sistem</p>
            <a href="{{ route('superadmin.admin.index') }}"
                class="inline-flex items-center text-blue-600 hover:text-blue-800">
                Lihat Detail
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-building mr-2 text-green-600"></i>Venue
                </h2>
                <span class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">
                    Total: {{ $venueCount ?? 0 }}
                </span>
            </div>
            <p class="text-gray-600 mb-4">Kelola semua venue dalam sistem</p>
            <a href="{{ route('superadmin.venue.index') }}"
                class="inline-flex items-center text-green-600 hover:text-green-800">
                Lihat Detail
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <!-- Sample activity items - in production, these would come from database -->
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Admin baru ditambahkan</p>
                        <p class="text-xs text-gray-500">2 jam yang lalu</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Venue baru ditambahkan</p>
                        <p class="text-xs text-gray-500">1 hari yang lalu</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Venue diperbarui</p>
                        <p class="text-xs text-gray-500">2 hari yang lalu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="rounded-full bg-blue-100 p-3">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 text-sm">Total Pengguna</h4>
                    <p class="text-2xl font-bold text-gray-800">{{ $userCount ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="rounded-full bg-green-100 p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 text-sm">Venue Aktif</h4>
                    <p class="text-2xl font-bold text-gray-800">{{ $activeVenueCount ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="rounded-full bg-purple-100 p-3">
                    <i class="fas fa-table text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-gray-500 text-sm">Total Meja</h4>
                    <p class="text-2xl font-bold text-gray-800">{{ $tableCount ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection