@extends('layouts.main')
@section('content')
    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Loading Overlay -->
    <div x-data="{ show: false }" x-show="show" x-cloak x-on:show-loading.window="show = true"
        x-on:hide-loading.window="show = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Memproses booking...</span>
            </div>
        </div>
    </div>

    <div class="min-h-96 mx-4 md:w-3/4 md:mx-auto">
        <div class="mb-6">
            <img src="{{ Storage::url($venue['image']) }}" alt="{{ $venue['name'] }}"
                class="w-full h-full object-cover rounded-lg mb-4 mt-8" />

            <h1 class="text-xl text-gray-800 font-semibold">{{ $venue['name'] }}</h1>
            <p class="text-sm text-gray-500">{{ $venue['description'] ?? 'Tidak ada deskripsi.' }}</p>
            @if($venue['status'] === 'open')
                {{-- Venue sedang buka - tampilkan jam operasional --}}
                <p class="text-sm text-gray-600 mt-1">
                    <i class="fa-regular fa-clock text-green-500"></i>
                    Jam Operasional: {{ date('H:i A', strtotime($venue['open_time'])) }} -
                    {{ date('H:i A', strtotime($venue['close_time'])) }}
                </p>
            @else
                {{-- Venue sedang tutup - tampilkan informasi penutupan --}}
                <div class="mt-1">
                    <p class="text-sm text-red-600 font-medium">
                        <i class="fa-solid fa-circle-xmark text-red-500"></i>
                        Tutup Sementara - {{ $venue['close_reason'] }}
                    </p>
                </div>
            @endif
        </div>
        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($venue['address']) }}" target="_blank"
            class="flex items-center bg-[url('/public/images/map.jpg')] bg-cover bg-center p-4">
            <div>
                <h1 class="font-semibold">Lokasi Venue</h1>
                <p>{{ $venue['address'] }}</p>
            </div>
            <div>
                <i class="fa-solid fa-map-pin text-red-800 text-3xl"></i>
            </div>
        </a>
        @auth
            <!-- Pending Bookings Section -->
            <div x-data="pendingBookingsComponent" class="mt-6">
                <template x-if="pendingBookings.length > 0">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <h2 class="font-semibold text-orange-700">
                                <i class="fa-solid fa-clock"></i> Booking yang Belum Diselesaikan
                            </h2>
                            <button @click="showPendingBookings = !showPendingBookings"
                                class="text-orange-700 hover:text-orange-900">
                                <span x-show="!showPendingBookings">▼ Lihat</span>
                                <span x-show="showPendingBookings">▲ Tutup</span>
                            </button>
                        </div>

                        <div x-show="showPendingBookings" x-transition class="mt-3">
                            <p class="text-sm text-orange-700 mb-2">Anda memiliki booking yang belum diselesaikan:</p>
                            <template x-for="booking in pendingBookings" :key="booking . id">
                                <div class="bg-white rounded-md p-3 mb-2 shadow-sm border border-orange-200">
                                    <div class="flex justify-between">
                                        <div>
                                            <p class="font-medium" x-text="booking.table.name"></p>
                                            <p class="text-sm text-gray-600"
                                                x-text="formatDateTime(booking.start_time) + ' - ' + formatTime(booking.end_time)">
                                            </p>
                                            <p class="text-sm font-medium text-gray-800 mt-1">
                                                <span>Rp </span>
                                                <span x-text="formatPrice(booking.total_amount)"></span>
                                            </p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button @click="resumeBooking(booking.id)"
                                                class="bg-blue-500 text-white text-sm px-3 py-1 rounded-md hover:bg-blue-600"
                                                :disabled="isLoadingPending">
                                                <template x-if="isLoadingPending">
                                                    <span>Loading...</span>
                                                </template>
                                                <template x-if="!isLoadingPending">
                                                    <span>Lanjutkan</span>
                                                </template>
                                            </button>
                                            <button @click="deletePendingBooking(booking.id)"
                                                class="bg-gray-200 text-gray-700 text-sm px-3 py-1 rounded-md hover:bg-gray-300"
                                                :disabled="isLoadingPending">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        @endauth

        <div class="mt-6">
            <div class="flex justify-between">
                <div>
                    <h1 class="text-xl text-gray-800 font-semibold">Pilih Meja</h1>
                </div>
                <div>
                    <h1 id="realTimeClock"></h1>
                </div>
            </div>
            @foreach ($venue['tables'] as $table)
                <div x-data="booking(
                    @json(auth()->check()), 
                    '{{ $table['id'] }}', 
                    {{ date('G', strtotime($venue['open_time'])) }},  // Jam buka (format 24 jam tanpa leading zero)
                    {{ date('G', strtotime($venue['close_time'])) }}, // Jam tutup
                    {{ $venue->is_overnight ? 'true' : 'false' }}     // Tambahkan flag is_overnight
                )"
                    class="border rounded-lg shadow-md p-4 mb-4">
                    <div class="flex items-center justify-between cursor-pointer"
                        @click="open = !open; if(open) checkBookedSchedules()">
                        <div class="flex items-center">
                            <img src="{{ asset('images/meja.jpg') }}" class="w-24">
                            <div class="ml-4">
                                <h3 class="font-semibold">{{ $table['name'] }} ({{ $table['brand'] }})</h3>
                                <p class="text-sm font-semibold text-gray-500">
                                    Rp. {{ number_format($table['price_per_hour'], 0, ',', '.') }} / jam
                                </p>
                            </div>
                        </div>
                        <div class="px-3 py-2 bg-gray-200 rounded-lg">
                            <span x-show="!open">▼</span>
                            <span x-show="open">▲</span>
                        </div>
                    </div>

                    <div x-show="open" x-collapse class="mt-4 p-4 border-t bg-gray-100 rounded-lg">
                        <h4 class="font-semibold mb-2">Pilih Jam Booking:</h4>
                        <select class="w-full border p-2 rounded-lg" x-model="selectedTime">
                            <option value="">-- Pilih Jam --</option>
                            <template x-for="hour in getAvailableHours()" :key="hour">
                                <option :value="hour + ':00'" :disabled="isTimeBooked(hour + ':00')"
                                    x-text="hour + ':00' + (isTimeBooked(hour + ':00') ? ' (Booked)' : '')">
                                </option>
                            </template>
                        </select>

                        <h4 class="font-semibold mb-2 mt-4">Pilih Durasi Main:</h4>
                            @if ($venue['status'] === 'open')
                                <select class="w-full border p-2 rounded-lg" x-model="selectedDuration">
                                    <option value="">-- Pilih Durasi --</option>
                                    <option value="1">1 Jam</option>
                                    <option value="2">2 Jam</option>
                                    <option value="3">3 Jam</option>
                                    {{-- <option value="4">4 Jam</option>
                                    <option value="5">5 Jam</option> --}}
                                </select>
                            @else
                                <select class="w-full border p-2 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed" disabled>
                                    <option value="">Venue sedang tutup</option>
                                </select>
                            @endif

                            <button
                                class="mt-3 px-4 py-2 rounded-lg w-full 
                                    {{ $venue['status'] === 'open' ? 'bg-green-500 text-white' : 'bg-gray-400 text-gray-700 cursor-not-allowed' }}"
                                :disabled="!selectedTime || !selectedDuration || isLoading || '{{ $venue['status'] }}' !== 'open'"
                                @click="initiateBooking('{{ $table['id'] }}', '{{ addslashes($table['name']) }}')">
                                
                                <template x-if="isLoading">
                                    <span>Loading...</span>
                                </template>
                                <template x-if="!isLoading">
                                    <span>Confirm Booking</span>
                                </template>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        // Toast Notification Function
        function showToast(message, type = 'info', duration = 5000) {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const bgColor = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'warning': 'bg-yellow-500',
                'info': 'bg-blue-500'
            }[type] || 'bg-blue-500';

            const icon = {
                'success': 'fa-check-circle',
                'error': 'fa-exclamation-circle',
                'warning': 'fa-exclamation-triangle',
                'info': 'fa-info-circle'
            }[type] || 'fa-info-circle';

            toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 min-w-80 transform transition-all duration-300 translate-x-full opacity-0`;
            toast.innerHTML = `
                                                                                                    <i class="fas ${icon}"></i>
                                                                                                    <span class="flex-1">${message}</span>
                                                                                                    <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
                                                                                                        <i class="fas fa-times"></i>
                                                                                                    </button>
                                                                                                `;

            toastContainer.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 100);

            // Auto remove
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }

        // Modal Notification Function
        function showModal(title, message, type = 'info', callback = null) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';

            const iconColor = {
                'success': 'text-green-500',
                'error': 'text-red-500',
                'warning': 'text-yellow-500',
                'info': 'text-blue-500'
            }[type] || 'text-blue-500';

            const icon = {
                'success': 'fa-check-circle',
                'error': 'fa-exclamation-circle',
                'warning': 'fa-exclamation-triangle',
                'info': 'fa-info-circle'
            }[type] || 'fa-info-circle';

            modal.innerHTML = `
                                                                                                    <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-2xl transform transition-all">
                                                                                                        <div class="flex items-center space-x-3 mb-4">
                                                                                                            <i class="fas ${icon} text-2xl ${iconColor}"></i>
                                                                                                            <h3 class="text-lg font-semibold text-gray-800">${title}</h3>
                                                                                                        </div>
                                                                                                        <p class="text-gray-600 mb-6">${message}</p>
                                                                                                        <div class="flex justify-end space-x-3">
                                                                                                            <button onclick="this.closest('.fixed').remove(); ${callback ? callback + '()' : ''}" 
                                                                                                                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
                                                                                                                OK
                                                                                                            </button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                `;

            document.body.appendChild(modal);

            // Close on backdrop click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                    if (callback) callback();
                }
            });
        }

        // Confirmation Modal Function
        function showConfirmModal(title, message, onConfirm, onCancel = null) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';

            modal.innerHTML = `
                                                                                                    <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-2xl transform transition-all">
                                                                                                        <div class="flex items-center space-x-3 mb-4">
                                                                                                            <i class="fas fa-question-circle text-2xl text-yellow-500"></i>
                                                                                                            <h3 class="text-lg font-semibold text-gray-800">${title}</h3>
                                                                                                        </div>
                                                                                                        <p class="text-gray-600 mb-6">${message}</p>
                                                                                                        <div class="flex justify-end space-x-3">
                                                                                                            <button id="cancelBtn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                                                                                                                Batal
                                                                                                            </button>
                                                                                                            <button id="confirmBtn" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors">
                                                                                                                Ya, Hapus
                                                                                                            </button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                `;

            document.body.appendChild(modal);

            const confirmBtn = modal.querySelector('#confirmBtn');
            const cancelBtn = modal.querySelector('#cancelBtn');

            confirmBtn.addEventListener('click', () => {
                modal.remove();
                if (onConfirm) onConfirm();
            });

            cancelBtn.addEventListener('click', () => {
                modal.remove();
                if (onCancel) onCancel();
            });

            // Close on backdrop click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                    if (onCancel) onCancel();
                }
            });
        }

        // Custom event for refreshing pending bookings across components
        const refreshPendingBookingsEvent = new Event('refresh-pending-bookings');

        // Tambahkan fungsi helper untuk mendapatkan tanggal Jakarta yang konsisten
        function getJakartaDate() {
            const now = new Date();
            // Buat objek Date dengan timezone Jakarta
            const jakartaTime = new Date(now.toLocaleString("en-US", { timeZone: "Asia/Jakarta" }));
            return jakartaTime;
        }

        function getJakartaDateString() {
            const jakartaTime = getJakartaDate();
            const year = jakartaTime.getFullYear();
            const month = String(jakartaTime.getMonth() + 1).padStart(2, '0');
            const day = String(jakartaTime.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function updateClock() {
            const jakartaTime = getJakartaDate();
            const timeFormatter = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
            document.getElementById('realTimeClock').textContent = timeFormatter.format(jakartaTime);
        }

        // Format functions for pending bookings
        function formatDateTime(dateTimeStr) {
            // Parse the datetime string
            const parts = dateTimeStr.split(/[^0-9]/);
            const year = parseInt(parts[0]);
            const month = parseInt(parts[1]) - 1; // JS months are 0-based
            const day = parseInt(parts[2]);
            const hour = parseInt(parts[3]);
            const minute = parseInt(parts[4]);

            // Gunakan zona waktu Asia/Jakarta (UTC+7)
            // Tambahkan 7 jam untuk mengkonversi dari UTC ke WIB
            const adjustedHour = (hour + 7) % 24;

            const dateFormatter = new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
            });

            // Format the date and time separately
            const dateObj = new Date(year, month, day);
            return dateFormatter.format(dateObj) + ' ' +
                (adjustedHour.toString().padStart(2, '0') + ':' +
                    minute.toString().padStart(2, '0'));
        }

        function formatTime(timeStr) {
            // Parse the ISO date string without timezone conversion
            const parts = timeStr.split(/[^0-9]/);
            const hour = parseInt(parts[3]);
            const minute = parseInt(parts[4]);

            // Tambahkan 7 jam untuk mengkonversi dari UTC ke WIB
            const adjustedHour = (hour + 7) % 24;

            // Format time manually
            return adjustedHour.toString().padStart(2, '0') + ':' +
                minute.toString().padStart(2, '0');
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('id-ID').format(price);
        }

        document.addEventListener('alpine:init', () => {
            // Pending bookings component
            Alpine.data('pendingBookingsComponent', () => ({
                pendingBookings: [],
                showPendingBookings: false,
                isLoadingPending: false,

                init() {
                    this.fetchPendingBookings();

                    // Listen for the custom event to refresh pending bookings
                    document.addEventListener('refresh-pending-bookings', () => {
                        console.log('Refreshing pending bookings from event');
                        this.fetchPendingBookings();
                        this.showPendingBookings = true; // Auto-expand the section
                    });
                },

                fetchPendingBookings() {
                    fetch('/booking/pending')
                        .then(response => response.json())
                        .then(data => {
                            // Filter bookings untuk venue saat ini jika diperlukan
                            const currentVenueId = {{ $venue['id'] ?? 'null' }};
                            if (currentVenueId) {
                                this.pendingBookings = data.filter(booking =>
                                    booking.table && booking.table.venue_id === currentVenueId
                                );
                            } else {
                                this.pendingBookings = data;
                            }

                            // Log jumlah pending bookings yang ditemukan
                            console.log("Found", this.pendingBookings.length, "pending bookings");

                            // If we have pending bookings and this was triggered by payment cancellation,
                            // make sure to show them
                            if (this.pendingBookings.length > 0 && window.justClosedPayment) {
                                this.showPendingBookings = true;
                                window.justClosedPayment = false;
                            }
                        })
                        .catch(error => console.error('Error fetching pending bookings:', error));
                },

                resumeBooking(bookingId) {
                    this.isLoadingPending = true;
                    window.dispatchEvent(new CustomEvent('show-loading'));

                    fetch(`/booking/pending/${bookingId}/resume`)
                        .then(response => response.json())
                        .then(data => {
                            window.dispatchEvent(new CustomEvent('hide-loading'));

                            if (data.success) {
                                console.log("Opening payment with snap token:", data.snap_token);
                                // Open Snap payment
                                window.snap.pay(data.snap_token, {
                                    onSuccess: (result) => {
                                        this.createBookingAfterPayment(data.order_id, result);
                                    },
                                    onPending: (result) => {
                                        showToast('Pembayaran pending, silahkan selesaikan pembayaran', 'warning');
                                        this.isLoadingPending = false;
                                    },
                                    onError: (result) => {
                                        showToast('Pembayaran gagal', 'error');
                                        this.isLoadingPending = false;
                                    },
                                    onClose: () => {
                                        showToast('Anda menutup popup tanpa menyelesaikan pembayaran', 'warning');
                                        this.isLoadingPending = false;
                                    }
                                });
                            } else {
                                showToast(data.message, 'error');
                                this.isLoadingPending = false;
                                // Refresh pending bookings list
                                this.fetchPendingBookings();
                            }
                        })
                        .catch(error => {
                            window.dispatchEvent(new CustomEvent('hide-loading'));
                            console.error('Error resuming booking:', error);
                            showToast('Gagal melanjutkan booking', 'error');
                            this.isLoadingPending = false;
                        });
                },

                deletePendingBooking(bookingId) {
                    showConfirmModal(
                        'Konfirmasi Hapus',
                        'Apakah Anda yakin ingin menghapus booking ini?',
                        () => {
                            this.isLoadingPending = true;
                            fetch(`/booking/pending/${bookingId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                }
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        showToast('Booking berhasil dihapus', 'success');
                                        this.fetchPendingBookings();
                                    } else {
                                        showToast(data.message, 'error');
                                    }
                                    this.isLoadingPending = false;
                                })
                                .catch(error => {
                                    console.error('Error deleting booking:', error);
                                    showToast('Gagal menghapus booking', 'error');
                                    this.isLoadingPending = false;
                                });
                        }
                    );
                },

                createBookingAfterPayment(orderId, paymentResult) {
                    window.dispatchEvent(new CustomEvent('show-loading'));

                    fetch('/booking', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            order_id: orderId,
                            transaction_id: paymentResult.transaction_id,
                            payment_method: paymentResult.payment_type,
                            transaction_status: paymentResult.transaction_status
                        }),
                    })
                        .then(res => {
                            if (!res.ok) {
                                return res.json().then(err => {
                                    throw new Error(err.message || 'Gagal menyimpan booking');
                                });
                            }
                            return res.json();
                        })
                        .then(data => {
                            window.dispatchEvent(new CustomEvent('hide-loading'));
                            showToast('Pembayaran dan booking berhasil!', 'success');
                            this.isLoadingPending = false;

                            // Refresh pending bookings list
                            this.fetchPendingBookings();

                            // Redirect to booking history
                            setTimeout(() => {
                                window.location.href = '/booking/history';
                            }, 2000);
                        })
                        .catch(err => {
                            window.dispatchEvent(new CustomEvent('hide-loading'));
                            console.error('Booking error:', err);
                            showToast('Pembayaran berhasil tetapi gagal menyimpan booking: ' + err.message, 'error');
                            this.isLoadingPending = false;
                        });
                }
            }));

            // Regular booking component (updated with dynamic hours)
             Alpine.data('booking', (isLoggedIn, tableId, openHour, closeHour, isOvernight) => ({
            isLoggedIn,
            tableId,
            openHour,
            closeHour,
            isOvernight,
            open: false,
            selectedTime: '',
            selectedDuration: '',
            isLoading: false,
            bookedSchedules: [],

                // Updated method to use dynamic hours from venue
                getAvailableHours() {
                let hours = [];
                const currentJakartaHour = getJakartaDate().getHours();

                if (this.isOvernight) {
                    // Jam dari waktu buka sampai tengah malam (23)
                    for (let i = this.openHour; i < 24; i++) {
                        // Hanya tampilkan jam yang akan datang
                        if (i >= currentJakartaHour) {
                            hours.push(i.toString().padStart(2, '0'));
                        }
                    }
                    // Jam dari tengah malam (00) sampai waktu tutup
                    for (let i = 0; i <= this.closeHour; i++) {
                        hours.push(i.toString().padStart(2, '0'));
                    }
                } else {
                    // Logika standar untuk venue yang tidak overnight
                    for (let i = this.openHour; i <= this.closeHour; i++) {
                        // Hanya tampilkan jam yang akan datang
                        if (i >= currentJakartaHour) {
                            hours.push(i.toString().padStart(2, '0'));
                        }
                    }
                }
                return hours;
            },

                isTimeBooked(time) {
                    const timeFormatted = time.padStart(5, '0');
                    return this.bookedSchedules.some(schedule => {
                        const isOvernightBooking = schedule.end < schedule.start;

                        if (isOvernightBooking) {
                            // Untuk booking overnight (misal 23:00 - 01:00)
                            // Slot dianggap booked jika:
                            // 1. Lebih besar atau sama dengan jam mulai (misal 23:00)
                            // ATAU
                            // 2. Lebih kecil dari jam selesai (misal 00:00)
                            return (timeFormatted >= schedule.start || timeFormatted < schedule.end);
                        } else {
                            // Untuk booking normal
                            return (timeFormatted >= schedule.start && timeFormatted < schedule.end);
                        }
                    });
                },

                async checkBookedSchedules() {
                    // Gunakan tanggal Jakarta yang konsisten
                    const today = getJakartaDateString();
                    try {
                        const response = await fetch(`/booking/schedules?table_id=${this.tableId}&date=${today}`);
                        this.bookedSchedules = await response.json();
                        console.log('Checking schedules for date:', today, 'Table:', this.tableId);
                        console.log('Booked schedules:', this.bookedSchedules);
                    } catch (error) {
                        console.error('Error checking booked schedules:', error);
                    }
                },

                initiateBooking(tableId, tableName) {
                    if (!this.isLoggedIn) {
                        showToast('Silahkan login terlebih dahulu untuk melakukan booking', 'warning');
                        return;
                    }
                    const selectedTime = this.selectedTime;
                    const selectedDuration = this.selectedDuration;

                    if (!selectedTime || !selectedDuration) {
                        showToast('Please select both time and duration', 'warning');
                        return;
                    }

                    // Validasi jam menggunakan waktu Jakarta
                    const now = getJakartaDate();
                    const selectedDateTime = new Date(now);
                    const [selectedHour, selectedMinute] = selectedTime.split(':').map(Number);
                    selectedDateTime.setHours(selectedHour, selectedMinute, 0, 0);

                    if (this.isOvernight && selectedHour < this.openHour) {
                    selectedDateTime.setDate(selectedDateTime.getDate() + 1);
                    }

                    // Uncomment this for production to prevent booking past times
                    if (selectedDateTime <= now) {
                        showToast('Tidak bisa booking untuk waktu yang sudah berlalu', 'warning');
                        return;
                    }

                    this.isLoading = true;
                    window.dispatchEvent(new CustomEvent('show-loading'));

                    // Gunakan tanggal Jakarta yang konsisten
                    const bookingDate = getJakartaDateString();

                    fetch('/booking/initiate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            table_id: tableId,
                            start_time: selectedTime,
                            duration: selectedDuration,
                            booking_date: bookingDate
                        }),
                    })
                        .then(res => res.json())
                        .then(data => {
                            window.dispatchEvent(new CustomEvent('hide-loading'));

                            if (data.success) {
                                // Cek apakah ini admin direct booking atau customer payment
                                if (data.snap_token) {
                                    // Customer biasa - perlu payment
                                    console.log("Opening payment with snap token:", data.snap_token);
                                    window.snap.pay(data.snap_token, {
                                        onSuccess: (result) => {
                                            this.createBooking(data.order_id, result);
                                        },
                                        onPending: (result) => {
                                            showToast('Pembayaran pending, silahkan selesaikan pembayaran', 'warning');
                                            this.isLoading = false;
                                        },
                                        onError: (result) => {
                                            showToast('Pembayaran gagal', 'error');
                                            this.isLoading = false;
                                        },
                                        onClose: () => {
                                            showToast('Anda menutup popup tanpa menyelesaikan pembayaran', 'warning');
                                            this.isLoading = false;
                                            window.justClosedPayment = true;

                                            // Dispatch event to refresh pending bookings
                                            document.dispatchEvent(refreshPendingBookingsEvent);
                                        }
                                    });
                                } else if (data.booking_id) {
                                    // Admin direct booking - langsung berhasil
                                    showToast(data.message || 'Booking berhasil dibuat!', 'success');
                                    this.isLoading = false;

                                    // Refresh halaman atau reload available times
                                    setTimeout(() => {
                                        window.location.reload(); // Atau panggil method refresh yang sudah ada
                                    }, 1000);
                                } else {
                                    // Response success tapi tidak ada snap_token atau booking_id
                                    showToast(data.message || 'Booking berhasil diproses', 'success');
                                    this.isLoading = false;
                                }
                            } else {
                                showToast(data.message, 'error');
                                this.isLoading = false;
                            }
                        })
                        .catch(err => {
                            window.dispatchEvent(new CustomEvent('hide-loading'));
                            console.error('Booking initiation error:', err);
                            showToast('Gagal melakukan booking', 'error');
                            this.isLoading = false;
                        });
                },

                createBooking(orderId, paymentResult) {
                    window.dispatchEvent(new CustomEvent('show-loading'));

                    fetch('/booking', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            order_id: orderId,
                            transaction_id: paymentResult.transaction_id,
                            payment_method: paymentResult.payment_type,
                            transaction_status: paymentResult.transaction_status
                        }),
                    })
                        .then(res => {
                            if (!res.ok) {
                                return res.json().then(err => {
                                    throw new Error(err.message || 'Gagal menyimpan booking');
                                });
                            }
                            return res.json();
                        })
                        .then(data => {
                            window.dispatchEvent(new CustomEvent('hide-loading'));
                            showToast('Pembayaran dan booking berhasil!', 'success');
                            this.isLoading = false;

                            // Reset form
                            this.selectedTime = '';
                            this.selectedDuration = '';
                            this.open = false;

                            // Refresh booked schedules
                            this.checkBookedSchedules();

                            // Redirect to booking history
                            setTimeout(() => {
                                window.location.href = '/booking/history';
                            }, 2000);
                        })
                        .catch(err => {
                            window.dispatchEvent(new CustomEvent('hide-loading'));
                            console.error('Booking error:', err);
                            showToast('Pembayaran berhasil tetapi gagal menyimpan booking: ' + err.message, 'error');
                            this.isLoading = false;
                        });
                }
            }));
        });

        // Initialize clock
        updateClock();
        setInterval(updateClock, 1000);
    </script>

@endsection