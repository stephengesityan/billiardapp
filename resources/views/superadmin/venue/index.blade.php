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
                    <img src="{{ asset('storage/' . ($venue->image ?? 'images/venue-placeholder.jpg')) }}"
                        alt="{{ $venue->name }}" class="w-full h-48 object-cover">
                    <div class="absolute top-3 right-3 flex gap-2">
                        <a href="{{ route('superadmin.venue.edit', $venue->id) }}"
                            class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" onclick="confirmDelete({{ $venue->id }})"
                            class="p-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $venue->name }}</h3>
                    <div class="flex items-center mb-2">
                        <i class="fas fa-map text-gray-500 mr-2"></i>
                        <span class="text-gray-600 truncate">{{ $venue->address }}</span>
                    </div>
                    <div class="flex items-center mb-2">
                        <i class="fas fa-phone text-gray-500 mr-2"></i>
                        <span class="text-gray-600">{{ $venue->phone }}</span>
                    </div>
                    <div class="flex items-center mb-4">
                        <i
                            class="fas fa-check-circle {{ $venue->status == 'active' ? 'text-green-500' : 'text-red-500' }} mr-2"></i>
                        <span class="text-gray-600">{{ $venue->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}</span>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">{{ $venue->created_at->format('d M Y') }}</span>
                            </div>
                            <a href="{{ route('superadmin.venue.edit', $venue->id) }}"
                                class="text-green-600 hover:text-green-800 flex items-center text-sm">
                                Detail
                                <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-lg shadow p-6 text-center">
                <i class="fas fa-building text-gray-300 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada venue</h3>
                <p class="text-gray-500 mb-4">Mulai tambahkan venue baru untuk mengelola bisnis Anda</p>
                <a href="{{ route('superadmin.venue.create') }}"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Venue
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($venues->hasPages())
        <div class="mt-6">
            {{ $venues->links() }}
        </div>
    @endif

    <!-- Delete Venue Confirmation Modal -->
    <div id="deleteVenueModal" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
        <div class="relative w-full h-full max-w-md md:h-auto">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Konfirmasi Hapus
                    </h3>
                    <button type="button" onclick="closeDeleteModal()"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6 text-center">
                    <i class="fas fa-exclamation-triangle text-5xl text-yellow-400 mb-4"></i>
                    <h3 class="mb-5 text-lg font-normal text-gray-500">Apakah Anda yakin ingin menghapus venue ini? Semua
                        data terkait dengan venue ini akan ikut terhapus.</h3>
                    <form id="deleteVenueForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                            Ya, saya yakin
                        </button>
                        <button type="button" onclick="closeDeleteModal()"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10">
                            Batal
                        </button>
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
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteVenueModal');
            modal.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Escape key closes modal
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeDeleteModal();
                }
            });

            // Click outside modal closes it
            window.onclick = function (event) {
                const modal = document.getElementById('deleteVenueModal');
                if (event.target === modal) {
                    closeDeleteModal();
                }
            }
        });
    </script>

@endsection