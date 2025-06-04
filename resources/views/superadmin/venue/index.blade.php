@extends('layouts.super-admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Manajemen Venue</h1>
            <p class="text-gray-600">Kelola semua venue dalam sistem</p>
        </div>
        <a href="{{ route('superadmin.venue.create') }}"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Venue
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Filter and Search -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form action="{{ route('superadmin.venue.index') }}" method="GET">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-grow">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                            class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            placeholder="Cari nama venue atau lokasi">
                    </div>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="self-end">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                        Filter
                    </button>
                    <a href="{{ route('superadmin.venue.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-md bg-white hover:bg-gray-50 text-sm ml-2">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Venue Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($venues as $venue)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="relative">
                    @php
                        // Tentukan path gambar yang akan ditampilkan
                        $imagePath = null;

                        if ($venue->image) {
                            // Cek apakah file gambar ada di storage
                            if (Storage::disk('public')->exists($venue->image)) {
                                $imagePath = asset('storage/' . $venue->image);
                            }
                            // Cek apakah file gambar ada di public folder
                            elseif (file_exists(public_path($venue->image))) {
                                $imagePath = asset($venue->image);
                            }
                            // Cek jika path sudah lengkap dengan storage/
                            elseif (file_exists(public_path('storage/' . $venue->image))) {
                                $imagePath = asset('storage/' . $venue->image);
                            }
                        }

                        // Fallback ke placeholder jika gambar tidak ditemukan
                        if (!$imagePath) {
                            $imagePath = asset('images/venue-placeholder.jpg');
                        }
                    @endphp

                    <img src="{{ $imagePath }}" alt="{{ $venue->name }}" class="w-full h-48 object-cover"
                        onerror="this.src='{{ asset('images/venue-placeholder.jpg') }}'; this.onerror=null;">

                    <div class="absolute top-3 right-3 flex gap-2">
                        <a href="{{ route('superadmin.venue.edit', $venue->id) }}"
                            class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" onclick="confirmDelete({{ $venue->id }})"
                            class="p-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $venue->name }}</h3>
                    <div class="flex items-center mb-2">
                        <i class="fas fa-map-marker-alt text-gray-500 mr-2"></i>
                        <span class="text-gray-600 truncate" title="{{ $venue->address }}">{{ $venue->address }}</span>
                    </div>
                    <div class="flex items-center mb-2">
                        <i class="fas fa-phone text-gray-500 mr-2"></i>
                        <span class="text-gray-600">{{ $venue->phone }}</span>
                    </div>
                    <div class="flex items-center mb-2">
                        <i class="fas fa-clock text-gray-500 mr-2"></i>
                        <span class="text-gray-600">
                            {{ $venue->open_time ?? '00:00' }} - {{ $venue->close_time ?? '23:59' }}
                        </span>
                    </div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-circle {{ $venue->status == 'active' ? 'text-green-500' : 'text-red-500' }} mr-2"></i>
                        <span class="text-sm font-medium {{ $venue->status == 'active' ? 'text-green-700' : 'text-red-700' }}">
                            {{ $venue->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>

                    @if($venue->description)
                        <div class="mb-4">
                            <p class="text-gray-600 text-sm line-clamp-2">{{ Str::limit($venue->description, 100) }}</p>
                        </div>
                    @endif

                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">{{ $venue->created_at->format('d M Y') }}</span>
                            </div>
                            <a href="{{ route('superadmin.venue.edit', $venue->id) }}"
                                class="text-green-600 hover:text-green-800 flex items-center text-sm transition-colors duration-200">
                                Detail
                                <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-lg shadow p-8 text-center">
                <div class="max-w-sm mx-auto">
                    <i class="fas fa-building text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum ada venue</h3>
                    <p class="text-gray-500 mb-6">Mulai tambahkan venue baru untuk mengelola bisnis Anda dengan lebih baik</p>
                    <a href="{{ route('superadmin.venue.create') }}"
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 inline-flex items-center transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Venue Pertama
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($venues->hasPages())
        <div class="mt-8">
            <div class="bg-white px-4 py-3 border border-gray-200 rounded-lg">
                {{ $venues->appends(request()->query())->links() }}
            </div>
        </div>
    @endif

    <!-- Delete Venue Confirmation Modal -->
    <div id="deleteVenueModal" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-gray-900 bg-opacity-50">
        <div class="relative w-full h-full max-w-md md:h-auto mx-auto flex items-center justify-center min-h-screen">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="flex items-center justify-between p-5 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Konfirmasi Hapus
                    </h3>
                    <button type="button" onclick="closeDeleteModal()"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors duration-200">
                        <i class="fas fa-times w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900">Hapus Venue</h3>
                    <p class="mb-6 text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus venue ini? Semua data terkait dengan venue ini akan ikut terhapus
                        dan tidak dapat dikembalikan.
                    </p>
                    <form id="deleteVenueForm" method="POST" action="" class="space-y-4">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-center space-x-3">
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                                Ya, Hapus
                            </button>
                            <button type="button" onclick="closeDeleteModal()"
                                class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(venueId) {
            const form = document.getElementById('deleteVenueForm');
            form.action = "{{ route('superadmin.venue.destroy', '') }}/" + venueId;

            // Show modal
            const modal = document.getElementById('deleteVenueModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteVenueModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto'; // Restore scrolling
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Escape key closes modal
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeDeleteModal();
                }
            });

            // Click outside modal closes it
            document.getElementById('deleteVenueModal').addEventListener('click', function (event) {
                if (event.target === this) {
                    closeDeleteModal();
                }
            });
        });

        // Image error handling function
        function handleImageError(img) {
            img.src = '{{ asset("images/venue-placeholder.jpg") }}';
            img.onerror = null; // Prevent infinite loop
        }
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

@endsection