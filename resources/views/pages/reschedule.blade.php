@extends('layouts.main')

@section('content')
<div class="min-h-96 mx-4 md:w-3/4 md:mx-auto py-8">
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <h1 class="text-2xl font-bold mb-6">Reschedule Booking</h1>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Detail Booking Saat Ini</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-500">Venue</p>
                <p class="font-medium">{{ $booking->table->venue->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Meja</p>
                <p class="font-medium">{{ $booking->table->name }} ({{ $booking->table->brand }})</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal & Waktu</p>
                <p class="font-medium">
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }},
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Durasi</p>
                <p class="font-medium">{{ $duration }} Jam</p>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <div class="mr-3 text-yellow-500"><i class="fa-solid fa-exclamation-circle text-xl"></i></div>
                <div>
                    <h3 class="font-semibold text-yellow-700">Perhatian</h3>
                    <p class="text-yellow-700 text-sm">
                        • Reschedule dapat dilakukan selama minimal 1 jam sebelum jadwal booking<br>
                        • Setiap booking hanya dapat di-reschedule maksimal 1 kali<br>
                        • Durasi booking akan tetap sama ({{ $duration }} jam)<br>
                        • Setelah reschedule, jadwal lama akan digantikan dengan jadwal baru
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div x-data="rescheduleForm" class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Pilih Jadwal Baru</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="date-picker" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Operasional:</label>
                <input type="date" id="date-picker" x-model="selectedDate" class="w-full border p-2 rounded-lg bg-gray-100 cursor-not-allowed" disabled>
            </div>

            <div>
                <label for="table-picker" class="block text-sm font-medium text-gray-700 mb-1">Pilih Meja:</label>
                <select id="table-picker" x-model="selectedTableId" class="w-full border p-2 rounded-lg" @change="fetchSchedules">
                    <option value="">-- Pilih Meja --</option>
                    <template x-for="table in tables" :key="table.id">
                        <option :value="table.id" x-text="table.name + ' (' + table.brand + ')'"></option>
                    </template>
                </select>
            </div>
        </div>

        <div class="mt-6" x-show="selectedDate && selectedTableId">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Jam Mulai:</label>
            <div x-show="!isLoadingSchedules" class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2">
                <template x-for="hour in availableHours" :key="hour">
                    <button class="py-2 px-1 rounded-lg text-sm font-medium transition duration-150"
                        :class="getSlotClass(hour)" :disabled="!isSlotAvailable(hour)" @click="selectedStartHour = hour"
                        x-text="hour + ':00'"></button>
                </template>
            </div>
            <div x-show="isLoadingSchedules" class="text-center text-gray-500 py-4">
                Memeriksa jadwal...
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <a href="{{ route('booking.history') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg mr-3 hover:bg-gray-300">Batal</a>
            <button @click="submitReschedule" :disabled="!canSubmit || isSubmitting"
                class="text-white px-4 py-2 rounded-lg flex items-center justify-center" :class="canSubmit ? 'bg-green-600 hover:bg-green-700' : 'bg-green-300 cursor-not-allowed'">
                <span x-show="!isSubmitting">Konfirmasi Reschedule</span>
                <span x-show="isSubmitting">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div> Memproses...
                </span>
            </button>
        </div>
    </div>
</div>

<script>
    function showNotification(message, type = 'info') { /* ... fungsi notifikasi tidak berubah ... */ }

    document.addEventListener('alpine:init', () => {
        Alpine.data('rescheduleForm', () => ({
            // PERUBAHAN 2: Inisialisasi data yang bersih & benar
            venue: @json($venue),
            bookingId: {{ $booking->id }},
            bookingDuration: {{ $duration }},
            tables: @json($venue->tables),

            selectedDate: '{{ $operational_date_string }}',
            selectedTableId: {{ $booking->table_id }},
            selectedStartHour: null,

            bookedSchedules: [],
            isLoadingSchedules: true,
            isSubmitting: false,

            // PERUBAHAN 3: Fungsi init() disederhanakan
            init() {
                this.fetchSchedules();
            },

            // Semua fungsi di bawah ini sudah benar & tidak perlu diubah lagi
            get openHour() { return parseInt(this.venue.open_time.split(':')[0]); },
            get closeHour() { return parseInt(this.venue.close_time.split(':')[0]); },
            get isOvernight() { return this.venue.is_overnight; },

            get availableHours() {
                let hours = [];
                if (this.isOvernight) {
                    for (let i = this.openHour; i < 24; i++) hours.push(i.toString().padStart(2, '0'));
                    for (let i = 0; i <= this.closeHour; i++) hours.push(i.toString().padStart(2, '0'));
                } else {
                    for (let i = this.openHour; i <= this.closeHour; i++) hours.push(i.toString().padStart(2, '0'));
                }
                return hours;
            },

            get canSubmit() {
                return this.selectedStartHour !== null && !this.isLoadingSchedules;
            },

            async fetchSchedules() {
                if (!this.selectedDate || !this.selectedTableId) return;
                this.isLoadingSchedules = true;
                this.bookedSchedules = [];
                this.selectedStartHour = null;
                try {
                    const response = await fetch(`/booking/reschedule/check-availability?table_id=${this.selectedTableId}&date=${this.selectedDate}&booking_id=${this.bookingId}`);
                    if (!response.ok) throw new Error('Failed to fetch schedules');
                    this.bookedSchedules = await response.json();
                } catch (error) {
                    console.error(error);
                } finally {
                    this.isLoadingSchedules = false;
                }
            },

            isSlotAvailable(hour) {
                const startHourInt = parseInt(hour);
                const endHourInt = startHourInt + this.bookingDuration;
                if (this.isOvernight) {
                    if (startHourInt >= this.openHour && endHourInt > (24 + this.closeHour)) return false;
                    if (startHourInt < this.openHour && endHourInt > this.closeHour) return false;
                } else {
                    if (endHourInt > this.closeHour) return false;
                }
                const now = new Date();
                const selectedDateTime = new Date(`${this.selectedDate}T${hour}:00:00`);
                if (this.isOvernight && startHourInt < this.openHour) {
                    selectedDateTime.setDate(selectedDateTime.getDate() + 1);
                }
                if (selectedDateTime <= now) {
                    return false;
                }
                for (const schedule of this.bookedSchedules) {
                    const scheduleStart = parseInt(schedule.start.split(':')[0]);
                    const scheduleEnd = parseInt(schedule.end.split(':')[0]);
                    const isOvernightBooking = scheduleEnd < scheduleStart;
                    if (isOvernightBooking) {
                        if ((startHourInt >= scheduleStart || startHourInt < scheduleEnd) && (endHourInt > scheduleStart || endHourInt <= scheduleEnd)) return false;
                    } else {
                        if (startHourInt < scheduleEnd && endHourInt > scheduleStart) return false;
                    }
                }
                return true;
            },

            getSlotClass(hour) {
                if (!this.isSlotAvailable(hour)) {
                    return 'bg-gray-200 text-gray-400 cursor-not-allowed';
                }
                if (this.selectedStartHour === hour) {
                    return 'bg-blue-600 text-white shadow-md';
                }
                return 'bg-gray-100 hover:bg-gray-200 text-gray-800';
            },

            async submitReschedule() {
                if (!this.canSubmit) return;
                this.isSubmitting = true;
                const startDateTime = new Date(`${this.selectedDate}T${this.selectedStartHour}:00:00`);
                if (this.isOvernight && parseInt(this.selectedStartHour) < this.openHour) {
                    startDateTime.setDate(startDateTime.getDate() + 1);
                }
                const endDateTime = new Date(startDateTime.getTime());
                endDateTime.setHours(endDateTime.getHours() + this.bookingDuration);
                const formatForServer = (date) => {
                    const pad = (num) => num.toString().padStart(2, '0');
                    const year = date.getFullYear();
                    const month = pad(date.getMonth() + 1);
                    const day = pad(date.getDate());
                    const hours = pad(date.getHours());
                    const minutes = pad(date.getMinutes());
                    const seconds = pad(date.getSeconds());
                    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                };
                try {
                    const response = await fetch(`/booking/${this.bookingId}/reschedule`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({
                            table_id: this.selectedTableId,
                            start_time: formatForServer(startDateTime),
                            end_time: formatForServer(endDateTime),
                        }),
                    });
                    const result = await response.json();
                    if (response.ok && result.success) {
                        showNotification('Booking berhasil di-reschedule!', 'success');
                        setTimeout(() => window.location.href = result.redirect, 1500);
                    } else { throw new Error(result.message || 'Gagal reschedule.'); }
                } catch (error) {
                    showNotification(error.message, 'error');
                } finally {
                    this.isSubmitting = false;
                }
            }
        }));
    });
</script>
@endsection