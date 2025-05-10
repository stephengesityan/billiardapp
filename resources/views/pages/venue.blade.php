@extends('layouts.main')
@section('content')
    <div class="min-h-96 mx-4 md:w-3/4 md:mx-auto">
        <div class="mb-6">
            <img src="{{ asset($venue['image']) }}" alt="{{ $venue['name'] }}" class="w-full rounded-lg mb-4 mt-8">
            <h1 class="text-xl text-gray-800 font-semibold">{{ $venue['name'] }}</h1>
            <p class="text-sm text-gray-500">{{ $venue['location'] }}</p>
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
                <div x-data="booking(@json(auth()->check()), '{{ $table['id'] }}')"
                    class="border rounded-lg shadow-md p-4 mb-4">
                    <div class="flex items-center justify-between cursor-pointer"
                        @click="open = !open; if(open) checkBookedSchedules()">
                        <div class="flex items-center">
                            <img src="{{ asset('images/meja.jpg') }}" class="w-24">
                            <div class="ml-4">
                                <h3 class="font-semibold">{{ $table['name'] }} ({{ $table['brand'] }})</h3>
                                <p class="text-sm">
                                    <span class="{{ $table['status'] == 'Available' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $table['status'] }}
                                    </span>
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
                            <template x-for="hour in getHoursInRange(9, 24)" :key="hour">
                                <option :value="hour + ':00'" :disabled="isTimeBooked(hour + ':00')"
                                    x-text="hour + ':00' + (isTimeBooked(hour + ':00') ? ' (Booked)' : '')">
                                </option>
                            </template>
                        </select>

                        <h4 class="font-semibold mb-2 mt-4">Pilih Durasi Main:</h4>
                        <select class="w-full border p-2 rounded-lg" x-model="selectedDuration">
                            <option value="">-- Pilih Durasi --</option>
                            <option value="1">1 Jam</option>
                            <option value="2">2 Jam</option>
                            <option value="3">3 Jam</option>
                        </select>

                        <button class="mt-3 px-4 py-2 bg-green-500 text-white rounded-lg w-full" :disabled="!selectedTime || !selectedDuration || isLoading"
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
        // Custom event for refreshing pending bookings across components
        const refreshPendingBookingsEvent = new Event('refresh-pending-bookings');

        function updateClock() {
            const now = new Date();
            const options = { timeZone: 'Asia/Jakarta', hour12: false };
            const timeFormatter = new Intl.DateTimeFormat('id-ID', { ...options, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('realTimeClock').textContent = timeFormatter.format(now);
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Format functions for pending bookings
        function formatDateTime(dateTimeStr) {
            // Parse the ISO date string without timezone conversion
            const parts = dateTimeStr.split(/[^0-9]/);
            const year = parseInt(parts[0]);
            const month = parseInt(parts[1]) - 1; // JS months are 0-based
            const day = parseInt(parts[2]);
            const hour = parseInt(parts[3]);
            const minute = parseInt(parts[4]);

            const dateFormatter = new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
            });

            // Format the date and time separately to avoid timezone issues
            const dateObj = new Date(year, month, day);
            return dateFormatter.format(dateObj) + ' ' +
                (hour.toString().padStart(2, '0') + ':' +
                    minute.toString().padStart(2, '0'));
        }

        function formatTime(timeStr) {
            // Parse the ISO date string without timezone conversion
            const parts = timeStr.split(/[^0-9]/);
            const hour = parseInt(parts[3]);
            const minute = parseInt(parts[4]);

            // Format time manually to avoid timezone issues
            return hour.toString().padStart(2, '0') + ':' +
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
                    fetch(`/booking/pending/${bookingId}/resume`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log("Opening payment with snap token:", data.snap_token);
                                // Open Snap payment
                                window.snap.pay(data.snap_token, {
                                    onSuccess: (result) => {
                                        this.createBookingAfterPayment(data.order_id, result);
                                    },
                                    onPending: (result) => {
                                        alert('Pembayaran pending, silahkan selesaikan pembayaran');
                                        this.isLoadingPending = false;
                                    },
                                    onError: (result) => {
                                        alert('Pembayaran gagal');
                                        this.isLoadingPending = false;
                                    },
                                    onClose: () => {
                                        alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                                        this.isLoadingPending = false;
                                    }
                                });
                            } else {
                                alert(data.message);
                                this.isLoadingPending = false;
                                // Refresh pending bookings list
                                this.fetchPendingBookings();
                            }
                        })
                        .catch(error => {
                            console.error('Error resuming booking:', error);
                            alert('Gagal melanjutkan booking');
                            this.isLoadingPending = false;
                        });
                },

                deletePendingBooking(bookingId) {
                    if (confirm('Apakah Anda yakin ingin menghapus booking ini?')) {
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
                                    alert('Booking berhasil dihapus');
                                    this.fetchPendingBookings();
                                } else {
                                    alert(data.message);
                                }
                                this.isLoadingPending = false;
                            })
                            .catch(error => {
                                console.error('Error deleting booking:', error);
                                alert('Gagal menghapus booking');
                                this.isLoadingPending = false;
                            });
                    }
                },

                createBookingAfterPayment(orderId, paymentResult) {
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
                            alert('Pembayaran dan booking berhasil!');
                            this.isLoadingPending = false;

                            // Refresh pending bookings list
                            this.fetchPendingBookings();

                            // Redirect to booking history
                            window.location.href = '/booking/history';
                        })
                        .catch(err => {
                            console.error('Booking error:', err);
                            alert('Pembayaran berhasil tetapi gagal menyimpan booking: ' + err.message);
                            this.isLoadingPending = false;
                        });
                }
            }));

            // Regular booking component (existing)
            Alpine.data('booking', (isLoggedIn, tableId) => ({
                isLoggedIn,
                tableId,
                open: false,
                selectedTime: '',
                selectedDuration: '',
                isLoading: false,
                bookedSchedules: [],

                getHoursInRange(startHour, endHour) {
                    let hours = [];
                    for (let i = startHour; i <= endHour; i++) {
                        hours.push(i.toString().padStart(2, '0'));
                    }
                    return hours;
                },

                isTimeBooked(time) {
                    const timeFormatted = time.padStart(5, '0');
                    return this.bookedSchedules.some(schedule => {
                        return timeFormatted >= schedule.start && timeFormatted < schedule.end;
                    });
                },

                async checkBookedSchedules() {
                    const today = new Date().toISOString().split('T')[0];
                    try {
                        const response = await fetch(`/booking/schedules?table_id=${this.tableId}&date=${today}`);
                        this.bookedSchedules = await response.json();
                    } catch (error) {
                        console.error('Error checking booked schedules:', error);
                    }
                },

                initiateBooking(tableId, tableName) {
                    if (!this.isLoggedIn) {
                        alert('Silahkan login terlebih dahulu untuk melakukan booking.');
                        return;
                    }
                    const selectedTime = this.selectedTime;
                    const selectedDuration = this.selectedDuration;

                    if (!selectedTime || !selectedDuration) {
                        alert('Please select both time and duration');
                        return;
                    }

                    // Validasi jam
                    const now = new Date();
                    const selectedDateTime = new Date();
                    const [selectedHour, selectedMinute] = selectedTime.split(':').map(Number);
                    selectedDateTime.setHours(selectedHour, selectedMinute, 0, 0);

                    // Uncomment this for production to prevent booking past times
                    // if (selectedDateTime <= now) {
                    //     alert('Jam yang dipilih sudah lewat. Silakan pilih jam yang masih tersedia.');
                    //     return;
                    // }

                    this.isLoading = true;

                    // Hitung end time
                    const bookingStart = new Date();
                    bookingStart.setHours(selectedHour, selectedMinute, 0, 0);
                    const bookingEnd = new Date(bookingStart);
                    bookingEnd.setHours(bookingEnd.getHours() + parseInt(selectedDuration));

                    const endTimeFormatted = ('0' + bookingEnd.getHours()).slice(-2) + ':' + ('0' + bookingEnd.getMinutes()).slice(-2);
                    const today = new Date().toISOString().split('T')[0];
                    const start_time = `${today} ${selectedTime}`;
                    const end_time = `${today} ${endTimeFormatted}`;

                    // Track that we're creating a new booking
                    window.creatingNewBooking = true;

                    // Kirim ke backend untuk membuat payment intent (tanpa membuat booking dulu)
                    fetch('/booking/payment-intent', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            table_id: tableId,
                            start_time: start_time,
                            end_time: end_time,
                        }),
                    })
                        .then(res => {
                            if (!res.ok) {
                                return res.json().then(err => {
                                    throw new Error(err.message || 'Gagal membuat payment intent');
                                });
                            }
                            return res.json();
                        })
                        .then(data => {
                            if (!data.snap_token) {
                                throw new Error('Snap token tidak ditemukan');
                            }

                            // Buka Snap Midtrans
                            window.snap.pay(data.snap_token, {
                                onSuccess: (result) => {
                                    this.createBookingAfterPayment(data.order_id, result);
                                },
                                onPending: (result) => {
                                    alert('Pembayaran pending, silahkan selesaikan pembayaran');
                                    this.isLoading = false;
                                },
                                onError: (result) => {
                                    alert('Pembayaran gagal');
                                    this.isLoading = false;
                                    // Reset the state
                                    window.creatingNewBooking = false;
                                },
                                onClose: () => {
                                    alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                                    this.isLoading = false;

                                    // Set flag to indicate payment popup was just closed
                                    window.justClosedPayment = true;

                                    // Only trigger the refresh if we were creating a new booking
                                    if (window.creatingNewBooking) {
                                        // Reset the flag
                                        window.creatingNewBooking = false;

                                        // Dispatch the custom event to refresh pending bookings
                                        document.dispatchEvent(refreshPendingBookingsEvent);
                                    }
                                }
                            });
                        })
                        .catch(err => {
                            console.error('Payment intent error:', err);
                            alert('Gagal membuat payment: ' + err.message);
                            this.isLoading = false;
                            window.creatingNewBooking = false;
                        });
                },

                // Fungsi untuk menyimpan booking setelah pembayaran berhasil
                createBookingAfterPayment(orderId, paymentResult) {
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
                            alert('Pembayaran dan booking berhasil!');

                            // Reset the flag
                            window.creatingNewBooking = false;

                            // Refresh component data
                            document.dispatchEvent(new CustomEvent('booking-completed'));

                            // Redirect to booking history page
                            window.location.href = '/booking/history';
                        })
                        .catch(err => {
                            console.error('Booking error:', err);
                            alert('Pembayaran berhasil tetapi gagal menyimpan booking: ' + err.message);
                            this.isLoading = false;
                            window.creatingNewBooking = false;
                        });
                },

                // Method to refresh booked schedules without reloading the page
                async refreshBookedSchedules() {
                    await this.checkBookedSchedules();
                }
            }));
        });
    </script>
@endsection