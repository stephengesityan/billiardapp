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
                <div x-data="booking(@json(auth()->check()), '{{ $table['id'] }}')" class="border rounded-lg shadow-md p-4 mb-4">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open; if(open) checkBookedSchedules()">
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
                            <template x-for="hour in getHoursInRange(9, 22)" :key="hour">
                                <option :value="hour + ':00'" 
                                        :disabled="isTimeBooked(hour + ':00')"
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
                            @click="submitBooking('{{ $table['id'] }}', '{{ addslashes($table['name']) }}')">
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
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        function updateClock() {
            const now = new Date();
            const options = { timeZone: 'Asia/Jakarta', hour12: false };
            const timeFormatter = new Intl.DateTimeFormat('id-ID', { ...options, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('realTimeClock').textContent = timeFormatter.format(now);
        }
        setInterval(updateClock, 1000);
        updateClock();

        document.addEventListener('alpine:init', () => {
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

                submitBooking(tableId, tableName) {
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

                    if (selectedDateTime <= now) {
                        alert('Jam yang dipilih sudah lewat. Silakan pilih jam yang masih tersedia.');
                        return;
                    }

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

                    // Kirim ke backend
                    fetch('/booking', {
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
                                throw new Error(err.message || 'Gagal membuat booking');
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
                            onSuccess: function(result) {
                                alert('Pembayaran berhasil!');
                                location.reload();
                            },
                            onPending: function(result) {
                                alert('Pembayaran pending, silahkan selesaikan pembayaran');
                            },
                            onError: function(result) {
                                alert('Pembayaran gagal');
                            },
                            onClose: function() {
                                alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                            }
                        });
                    })
                    .catch(err => {
                        console.error('Booking error:', err);
                        alert('Gagal booking: ' + err.message);
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
                }
            }))
        })
    </script>
@endsection