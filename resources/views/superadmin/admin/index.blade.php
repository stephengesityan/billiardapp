@extends('layouts.super-admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Manajemen Admin</h1>
            <p class="text-gray-600">Kelola admin untuk setiap venue</p>
        </div>
        <a href="{{ route('superadmin.admin.create') }}"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Admin
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Filter and Search -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form action="{{ route('superadmin.admin.index') }}" method="GET">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-grow">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                            class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Cari nama atau email">
                    </div>
                </div>
                <div class="md:w-64">
                    <label for="venue_filter" class="block text-sm font-medium text-gray-700 mb-1">Filter Venue</label>
                    <select id="venue_filter" name="venue_id"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Venue</option>
                        @foreach($venues as $venue)
                            <option value="{{ $venue->id }}" {{ request('venue_id') == $venue->id ? 'selected' : '' }}>
                                {{ $venue->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="self-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                        Cari
                    </button>
                    <a href="{{ route('superadmin.admin.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-md bg-white hover:bg-gray-50 text-sm ml-2">
                        Reset Filter
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Admin Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Venue
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Terdaftar
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($admins as $admin)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $admin->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($admin->venue)
                                        {{ $admin->venue->name }}
                                    @else
                                        <span class="text-gray-400">Tidak ada venue</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $admin->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $admin->email_verified_at ? 'Terverifikasi' : 'Belum Verifikasi' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $admin->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('superadmin.admin.edit', $admin->id) }}"
                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" data-admin-id="{{ $admin->id }}" data-admin-name="{{ $admin->name }}"
                                    class="text-red-600 hover:text-red-800 delete-admin-btn">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data admin ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $admins->links() }}
        </div>
    </div>

    <!-- Delete Admin Confirmation Modal -->
    <div id="deleteAdminModal" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
        <div class="relative w-full h-full max-w-md md:h-auto">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Konfirmasi Hapus
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                        data-modal-hide="deleteAdminModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6 text-center">
                    <i class="fas fa-exclamation-triangle text-5xl text-yellow-400 mb-4"></i>
                    <h3 class="mb-5 text-lg font-normal text-gray-500">Apakah Anda yakin ingin menghapus admin <span
                            id="admin-name-to-delete" class="font-medium"></span>?</h3>
                    <form id="deleteAdminForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                            Ya, saya yakin
                        </button>
                        <button type="button" data-modal-hide="deleteAdminModal"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10">
                            Batal
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle delete admin button clicks
            const deleteButtons = document.querySelectorAll('.delete-admin-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const adminId = this.getAttribute('data-admin-id');
                    const adminName = this.getAttribute('data-admin-name');
                    const form = document.getElementById('deleteAdminForm');
                    form.action = "{{ route('superadmin.admin.destroy', '') }}/" + adminId;
                    // Set the admin name in the confirmation message
                    document.getElementById('admin-name-to-delete').textContent = adminName;

                    // Show the delete confirmation modal
                    const modal = document.getElementById('deleteAdminModal');
                    modal.classList.remove('hidden');
                });
            });

            // Modal hide functionality
            const modalHideButtons = document.querySelectorAll('[data-modal-hide]');
            modalHideButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const modalId = this.getAttribute('data-modal-hide');
                    document.getElementById(modalId).classList.add('hidden');
                });
            });
        });
    </script>
@endsection