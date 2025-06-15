@extends('layouts.admin')

@section('content')
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <!-- Judul -->
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Kelola Venue</h1>
                    <p class="text-gray-600 mt-1">Kelola informasi venue Anda</p>
                </div>

                <!-- Aksi -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <!-- Status Venue -->
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Status Venue:</span>
                        <div class="relative">
                            <button id="statusToggle"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ $venue->status === 'open' ? 'bg-green-600' : 'bg-red-600' }}"
                                onclick="toggleVenueStatus()">
                                <span class="sr-only">Toggle venue status</span>
                                <span
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $venue->status === 'open' ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </div>
                        <span id="statusText"
                            class="text-sm font-medium {{ $venue->status === 'open' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $venue->status === 'open' ? 'Buka' : 'Tutup' }}
                        </span>
                    </div>

                    <!-- Tombol Edit -->
                    <a href="{{ route('admin.venue.edit') }}"
                        class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow transition duration-300 w-full sm:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Venue
                    </a>
                </div>
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

                            <!-- Status -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-medium text-gray-500">Status:</span>
                                </div>
                                <div class="flex-1">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $venue->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $venue->status === 'open' ? 'Buka' : 'Tutup' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Operating Hours -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-medium text-gray-500">Jam Operasional:</span>
                                </div>
                                <div class="flex-1">
                                    @if($venue->status === 'open')
                                        <span class="text-sm text-gray-900">
                                            {{ $venue->open_time ? \Carbon\Carbon::parse($venue->open_time)->format('H:i') : '-' }}
                                            -
                                            {{ $venue->close_time ? \Carbon\Carbon::parse($venue->close_time)->format('H:i') : '-' }}
                                        </span>
                                    @else
                                        <span class="text-sm text-red-600">Tutup Sementara</span>
                                        @if($venue->original_open_time && $venue->original_close_time)
                                            <div class="text-xs text-gray-500 mt-1">
                                                Jam normal: {{ \Carbon\Carbon::parse($venue->original_open_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($venue->original_close_time)->format('H:i') }}
                                            </div>
                                        @endif
                                    @endif
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

        <!-- Close Venue Modal -->
        <div id="closeVenueModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Tutup Venue</h3>
                        <div class="mt-4 text-left">
                            <form id="closeVenueForm">
                                <div class="mb-4">
                                    <label for="closeReason" class="block text-sm font-medium text-gray-700 mb-2">Alasan
                                        Penutupan *</label>
                                    <textarea id="closeReason" name="close_reason" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Masukkan alasan penutupan venue..." required></textarea>
                                    <div id="closeReasonError" class="text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                                <div class="mb-4">
                                    <label for="reopenDate" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
                                        Buka
                                        Kembali *</label>
                                    <input type="date" id="reopenDate" name="reopen_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                    <div id="reopenDateError" class="text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="button" onclick="confirmCloseVenue()"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            Tutup Venue
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-4 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="text-gray-700">Memproses...</span>
                </div>
            </div>
        </div>

        <script>
            let currentVenueStatus = '{{ $venue->status }}';

            function toggleVenueStatus() {
                if (currentVenueStatus === 'open') {
                    // Show close venue modal
                    document.getElementById('closeVenueModal').classList.remove('hidden');
                } else {
                    // Open venue directly
                    confirmToggleStatus();
                }
            }

            function closeModal() {
                document.getElementById('closeVenueModal').classList.add('hidden');
                // Clear form
                document.getElementById('closeVenueForm').reset();
                clearErrors();
            }

            function clearErrors() {
                document.getElementById('closeReasonError').classList.add('hidden');
                document.getElementById('reopenDateError').classList.add('hidden');
            }

            function confirmCloseVenue() {
                const closeReason = document.getElementById('closeReason').value.trim();
                const reopenDate = document.getElementById('reopenDate').value;

                // Clear previous errors
                clearErrors();

                // Validate form
                let hasError = false;

                if (!closeReason) {
                    document.getElementById('closeReasonError').textContent = 'Alasan penutupan harus diisi.';
                    document.getElementById('closeReasonError').classList.remove('hidden');
                    hasError = true;
                }

                if (!reopenDate) {
                    document.getElementById('reopenDateError').textContent = 'Tanggal buka kembali harus diisi.';
                    document.getElementById('reopenDateError').classList.remove('hidden');
                    hasError = true;
                } else {
                    const today = new Date();
                    const selectedDate = new Date(reopenDate);
                    if (selectedDate <= today) {
                        document.getElementById('reopenDateError').textContent = 'Tanggal buka kembali harus setelah hari ini.';
                        document.getElementById('reopenDateError').classList.remove('hidden');
                        hasError = true;
                    }
                }

                if (hasError) {
                    return;
                }

                // Close modal and proceed with toggle
                closeModal();
                confirmToggleStatus({
                    close_reason: closeReason,
                    reopen_date: reopenDate
                });
            }

            function confirmToggleStatus(data = {}) {
                // Show loading spinner
                document.getElementById('loadingSpinner').classList.remove('hidden');

                // Prepare request data
                const requestData = {
                    _token: '{{ csrf_token() }}',
                    ...data
                };

                fetch('{{ route("admin.venue.toggle-status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(requestData)
                })
                    .then(response => response.json())
                    .then(data => {
                        // Hide loading spinner
                        document.getElementById('loadingSpinner').classList.add('hidden');

                        if (data.success) {
                            // Update UI
                            updateVenueStatusUI(data.venue);

                            // Show success message
                            showAlert(data.message, 'success');

                            // Update current status
                            currentVenueStatus = data.status;

                            // Reload page after 2 seconds to refresh all data
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            if (data.errors) {
                                // Handle validation errors
                                let errorMessage = 'Terjadi kesalahan validasi:\n';
                                Object.values(data.errors).forEach(error => {
                                    errorMessage += '- ' + error[0] + '\n';
                                });
                                showAlert(errorMessage, 'error');
                            } else {
                                showAlert(data.error || 'Terjadi kesalahan yang tidak diketahui', 'error');
                            }
                        }
                    })
                    .catch(error => {
                        // Hide loading spinner
                        document.getElementById('loadingSpinner').classList.add('hidden');

                        console.error('Error:', error);
                        showAlert('Venue ditutup sementara!', 'error');

                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    });
            }

            function updateVenueStatusUI(venue) {
                const toggle = document.getElementById('statusToggle');
                const statusText = document.getElementById('statusText');
                const toggleButton = toggle.querySelector('span:last-child');

                if (venue.status === 'open') {
                    toggle.classList.remove('bg-red-600');
                    toggle.classList.add('bg-green-600');
                    toggleButton.classList.remove('translate-x-1');
                    toggleButton.classList.add('translate-x-6');
                    statusText.textContent = 'Buka';
                    statusText.classList.remove('text-red-600');
                    statusText.classList.add('text-green-600');
                } else {
                    toggle.classList.remove('bg-green-600');
                    toggle.classList.add('bg-red-600');
                    toggleButton.classList.remove('translate-x-6');
                    toggleButton.classList.add('translate-x-1');
                    statusText.textContent = 'Tutup';
                    statusText.classList.remove('text-green-600');
                    statusText.classList.add('text-red-600');
                }
            }

            function showAlert(message, type) {
                // Remove existing alerts
                const existingAlerts = document.querySelectorAll('.alert-message');
                existingAlerts.forEach(alert => alert.remove());

                // Create new alert
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert-message mb-6 px-4 py-3 rounded-lg ${type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'
                    }`;
                alertDiv.textContent = message;

                // Insert alert after header
                const header = document.querySelector('.mb-6');
                header.parentNode.insertBefore(alertDiv, header.nextSibling);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }

            // Set minimum date for reopen date input
            document.addEventListener('DOMContentLoaded', function () {
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                const minDate = tomorrow.toISOString().split('T')[0];
                document.getElementById('reopenDate').setAttribute('min', minDate);
            });
        </script>
@endsection