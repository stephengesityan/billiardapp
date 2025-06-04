@extends('layouts.admin')

@section('content')
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-white">Detail Pendapatan Meja: {{ $table->name }}</h2>
                    <a href="{{ route('admin.revenues.index', ['venue_id' => $table->venue_id, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                       class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                </div>
                <div class="p-6">
                    <!-- Info Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl shadow p-6 border border-indigo-100 transform transition-all duration-200 hover:scale-105">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Venue</h3>
                            <p class="text-xl font-semibold text-gray-800">{{ $table->venue->name }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl shadow p-6 border border-blue-100 transform transition-all duration-200 hover:scale-105">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Total Pendapatan</h3>
                            <p class="text-xl font-semibold text-blue-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow p-6 border border-green-100 transform transition-all duration-200 hover:scale-105">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Total Jam Terpakai</h3>
                            <p class="text-xl font-semibold text-gray-800">{{ $totalHours }} jam</p>
                        </div>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.revenues.detail', $table->id) }}" class="mb-8 bg-gray-50 rounded-lg border border-gray-200 p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                      id="start_date" name="start_date" value="{{ $startDate }}">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                                <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                      id="end_date" name="end_date" value="{{ $endDate }}">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Bookings Table -->
                    <div class="mb-8">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-800">Daftar Booking</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Booking</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Selesai</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi (Jam)</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode Pembayaran</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pembayaran</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($bookings as $booking)
                                                                            <tr class="hover:bg-gray-50">
                                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->id }}</td>
                                                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $booking->user->name }}</td>
                                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->start_time->format('d M Y, H:i') }}</td>
                                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->end_time->format('d M Y, H:i') }}</td>
                                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                                    @php
                                                                                        $hours = $booking->start_time->diffInHours($booking->end_time);
                                                                                        $minutes = $booking->start_time->copy()->addHours($hours)->diffInMinutes($booking->end_time);
                                                                                        echo $hours . ($minutes > 0 ? '.' . $minutes : '');
                                                                                    @endphp
                                                                                </td>
                                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                                                        {{ $booking->payment_method == 'transfer' ? 'bg-blue-100 text-blue-800' :
                                            ($booking->payment_method == 'cash' ? 'bg-green-100 text-green-800' :
                                                'bg-purple-100 text-purple-800') }}">
                                                                                        {{ $booking->payment_method }}
                                                                                    </span>
                                                                                </td>
                                                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                                    Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                                                                                </td>
                                                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <p class="mt-2 font-medium">Tidak ada data booking untuk periode ini</p>
                                                    <p class="mt-1 text-gray-400">Coba ubah filter tanggal untuk melihat data lainnya.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- <!-- Chart: Usage Patterns -->
                    <div>
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-800">Pola Penggunaan Meja</h3>
                            </div>
                            <div class="p-6">
                                <canvas id="usagePatternChart" height="300"></canvas>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get the booking data
            const bookings = @json($bookings);

            // Prepare data for usage patterns by hour of day
            const hourCounts = Array(24).fill(0);
            const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const dayOfWeekCounts = Array(7).fill(0);

            bookings.forEach(booking => {
                const startTime = new Date(booking.start_time);
                hourCounts[startTime.getHours()]++;
                dayOfWeekCounts[startTime.getDay()]++;
            });

            // Create the hour of day chart
            const ctx = document.getElementById('usagePatternChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Array.from({ length: 24 }, (_, i) => `${i}:00`),
                    datasets: [{
                        label: 'Jumlah Booking berdasarkan Jam',
                        data: hourCounts,
                        backgroundColor: 'rgba(79, 70, 229, 0.7)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.8)',
                            titleFont: {
                                family: "'Inter', sans-serif",
                                size: 13
                            },
                            bodyFont: {
                                family: "'Inter', sans-serif",
                                size: 12
                            },
                            callbacks: {
                                label: function (context) {
                                    const value = context.raw;
                                    return `${value} booking${value !== 1 ? 's' : ''}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            },
                            ticks: {
                                stepSize: 1,
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 10
                                },
                                maxRotation: 0,
                                autoSkip: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection