@extends('layouts.admin')

@section('content')
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Kelola Venue</h1>
                    <p class="text-gray-600 mt-1">Kelola informasi venue Anda</p>
                </div>
                <a href="{{ route('admin.venue.edit') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Venue
                </a>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Venue Information Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Venue Image -->
                    <div class="lg:col-span-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Foto Venue</h3>
                        <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-gray-100">
                            @if($venue->image)
                                <img src="{{ asset('storage/' . $venue->image) }}" alt="{{ $venue->name }}"
                                    class="w-full h-48 object-cover rounded-lg">
                            @else
                                <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Venue Details -->
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Venue</h3>

                        <div class="space-y-4">
                            <!-- Venue Name -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-medium text-gray-500">Nama Venue:</span>
                                </div>
                                <div class="flex-1">
                                    <span class="text-sm text-gray-900 font-medium">{{ $venue->name }}</span>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-medium text-gray-500">Alamat:</span>
                                </div>
                                <div class="flex-1">
                                    <span class="text-sm text-gray-900">{{ $venue->address }}</span>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-medium text-gray-500">Telepon:</span>
                                </div>
                                <div class="flex-1">
                                    <span class="text-sm text-gray-900">{{ $venue->phone ?: '-' }}</span>
                                </div>
                            </div>

                            <!-- Operating Hours -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-medium text-gray-500">Jam Operasional:</span>
                                </div>
                                <div class="flex-1">
                                    <span class="text-sm text-gray-900">
                                        {{ $venue->open_time ? \Carbon\Carbon::parse($venue->open_time)->format('H:i') : '-' }}
                                        -
                                        {{ $venue->close_time ? \Carbon\Carbon::parse($venue->close_time)->format('H:i') : '-' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-medium text-gray-500">Deskripsi:</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900">{{ $venue->description ?: 'Belum ada deskripsi' }}</p>
                                </div>
                            </div>

                            <!-- Last Updated -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-medium text-gray-500">Terakhir Diperbarui:</span>
                                </div>
                                <div class="flex-1">
                                    <span
                                        class="text-sm text-gray-900">{{ $venue->updated_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <!-- Total Tables -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Meja</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $venue->tables->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Available Tables -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Meja Tersedia</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ $venue->tables->where('status', 'available')->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Occupied Tables -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Meja Terpakai</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ $venue->tables->where('status', 'occupied')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection