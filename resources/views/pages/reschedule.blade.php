@extends('layouts.main')
@section('content')
<div class="min-h-96 mx-4 md:w-3/4 md:mx-auto py-8">
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
                <div class="mr-3 text-yellow-500">
                    <i class="fa-solid fa-exclamation-circle text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-yellow-700">Perhatian</h3>
                    <p class="text-yellow-700 text-sm">
                        • Reschedule hanya dapat dilakukan 1x untuk setiap booking<br>
                        • Batas waktu reschedule adalah 1 jam sebelum jadwal booking<br>
                        • Durasi booking akan tetap sama ({{ $duration }} jam)
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div x-data="rescheduleForm" class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Pilih Jadwal Baru</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal:</label>
                <input type="date" x-model="selectedDate" class="w-full border p-2 rounded-lg" 
                       :min="today" @change="dateChanged">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Meja:</label>
                <select x-model="selectedTableId" class="w-full border p-2 rounded-lg" @change="tableChanged">
                    <option value="">-- Pilih Meja --</option>
                    <template x-for="table in tables" :key="table.id">
                        <option :value="table.id" x-text="table.name + ' (' + table.brand + ')'"></option>
                    </template>
                </select>
            </div>
        </div>
        
        <div class="mt-6" x-show="selectedDate && selectedTableId">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Jam Mulai:</label>
            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2">
                <template x-for="hour in availableHours" :key="hour">
                    <button 
                        class="py-2 px-1 rounded-lg text-sm font-medium transition duration-150"
                        :class="isTimeSlotAvailable(hour) ? 
                                (selectedStartHour === hour ? 'bg-blue-500 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-800') : 
                                'bg-gray-200 text-gray-400 cursor-not-allowed opacity-70'"
                        :disabled="!isTimeSlotAvailable(hour)"
                        @click="selectStartHour(hour)"
                        x-text="hour + ':00'">
                    </button>
                </template>
            </div>
            
            <div class="mt-4" x-show="selectedStartHour">
                <p class="text-sm text-gray-700 mb-2">
                    Jadwal reschedule: <span class="font-medium" x-text="formattedSchedule"></span>
                </p>
            </div>
        </div>
        
        <div class="mt-8 flex justify-end">
            <a href="{{ route('booking.history') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg mr-3">
                Batal
            </a>
            <button @click="submitReschedule" 
                    :disabled="!canSubmit || isSubmitting"
                    :class="canSubmit ? 'bg-green-500 hover:bg-green-600' : 'bg-green-300 cursor-not-allowed'"
                    class="text-white px-4 py-2 rounded-lg">
                <span x-show="!isSubmitting">Konfirmasi Reschedule</span>
                <span x-show="isSubmitting">Memproses...</span>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('rescheduleForm', () => ({
            tables: @json($venue->tables),
            bookingId: {{ $booking->id }},
            bookingDuration: {{ $duration }},
            originalTableId: {{ $booking->table_id }},
            selectedDate: '',
            selectedTableId: '',
            selectedStartHour: null,
            bookedSchedules: [],
            availableHours: Array.from({length: 16}, (_, i) => (i + 9).toString().padStart(2, '0')), // 9AM to 24PM
            isSubmitting: false,
            
            init() {
                // Set today as default value
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                this.today = `${year}-${month}-${day}`;
                this.selectedDate = this.today;
                
                // Set original table as default
                this.selectedTableId = this.originalTableId;
                
                // Load schedules for today and selected table
                this.checkBookedSchedules();
            },
            
            get canSubmit() {
                return this.selectedDate && this.selectedTableId && this.selectedStartHour !== null;
            },
            
            get formattedSchedule() {
                if (!this.selectedStartHour) return '';
                
                const startHour = parseInt(this.selectedStartHour);
                const endHour = startHour + this.bookingDuration;
                
                return `${this.selectedStartHour}:00 - ${endHour.toString().padStart(2, '0')}:00`;
            },
            
            async dateChanged() {
                this.selectedStartHour = null;
                await this.checkBookedSchedules();
            },
            
            async tableChanged() {
                this.selectedStartHour = null;
                await this.checkBookedSchedules();
            },
            
            async checkBookedSchedules() {
                if (!this.selectedDate || !this.selectedTableId) return;
                
                try {
                    const response = await fetch(`/booking/reschedule/check-availability?table_id=${this.selectedTableId}&date=${this.selectedDate}&booking_id=${this.bookingId}`);
                    this.bookedSchedules = await response.json();
                } catch (error) {
                    console.error('Error checking booked schedules:', error);
                }
            },
            
            isTimeSlotAvailable(hour) {
                const hourInt = parseInt(hour);
                const endHourInt = hourInt + this.bookingDuration;
                
                // Check if slot end time exceeds midnight
                if (endHourInt > 24) return false;
                
                // Check if any existing booking overlaps with this slot
                return !this.bookedSchedules.some(schedule => {
                    const scheduleStart = parseInt(schedule.start.split(':')[0]);
                    const scheduleEnd = parseInt(schedule.end.split(':')[0]);
                    
                    // Check if there's overlap
                    return (hourInt < scheduleEnd && endHourInt > scheduleStart);
                });
            },
            
            selectStartHour(hour) {
                if (this.isTimeSlotAvailable(hour)) {
                    this.selectedStartHour = hour;
                }
            },
            
            async submitReschedule() {
                if (!this.canSubmit || this.isSubmitting) return;
                
                this.isSubmitting = true;
                
                // Calculate end time based on selected start hour and duration
                const startHour = parseInt(this.selectedStartHour);
                const endHour = startHour + this.bookingDuration;
                
                // Format date strings for API
                const startTime = `${this.selectedDate} ${this.selectedStartHour}:00:00`;
                const endTime = `${this.selectedDate} ${endHour.toString().padStart(2, '0')}:00:00`;
                
                try {
                    const response = await fetch(`/booking/${this.bookingId}/reschedule`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            table_id: this.selectedTableId,
                            start_time: startTime,
                            end_time: endTime,
                        }),
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert(result.message);
                        window.location.href = result.redirect;
                    } else {
                        alert(result.message || 'Terjadi kesalahan saat memproses reschedule.');
                        this.isSubmitting = false;
                    }
                } catch (error) {
                    console.error('Error submitting reschedule:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                    this.isSubmitting = false;
                }
            }
        }));
    });
</script>
@endsection