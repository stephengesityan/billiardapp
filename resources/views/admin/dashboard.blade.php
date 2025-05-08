@extends('layouts.admin')

@section('content')
    <div class="bg-gray-50 min-h-screen">
        <div class="p-6">
            <!-- Header and Welcome -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard {{ $venue->name }}</h1>
                    <p class="text-gray-600 mt-1">Selamat datang, {{ auth()->user()->name }}!</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ now()->format('l, d F Y') }}</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ now()->format('H:i') }}</p>
                </div>
            </div>

            <!-- Stats Cards - Row 1 -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Today's Revenue -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Pendapatan Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-800">Rp{{ number_format($todayRevenue, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-green-500 p-2 bg-green-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Pendapatan Bulan Ini: Rp{{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
                </div>

                <!-- Today's Bookings -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Booking Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $todayBookings }}</p>
                        </div>
                        <div class="text-blue-500 p-2 bg-blue-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex mt-2 space-x-4">
                        <p class="text-xs text-gray-500">Pending: <span class="font-semibold text-amber-500">{{ $pendingBookings }}</span></p>
                        <p class="text-xs text-gray-500">Paid: <span class="font-semibold text-green-500">{{ $paidBookings }}</span></p>
                    </div>
                </div>

                <!-- Total Tables -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Meja</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $totalTables }}</p>
                        </div>
                        <div class="text-purple-500 p-2 bg-purple-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex mt-2 space-x-4">
                        <p class="text-xs text-gray-500">Tersedia: <span class="font-semibold text-green-500">{{ $availableTables }}</span></p>
                        <p class="text-xs text-gray-500">Digunakan: <span class="font-semibold text-red-500">{{ $usedTables }}</span></p>
                    </div>
                </div>

                <!-- Table Usage -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-amber-500 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Penggunaan Meja</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $totalTables > 0 ? round(($usedTables / $totalTables) * 100) : 0 }}%</p>
                        </div>
                        <div class="text-amber-500 p-2 bg-amber-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-3">
                        <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ $totalTables > 0 ? ($usedTables / $totalTables) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Charts and Recent Bookings -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Weekly Bookings Chart -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-semibold text-lg mb-4">Booking Per Hari (7 Hari Terakhir)</h2>
                    <div class="h-80" id="bookingsChart"></div>
                </div>

                <!-- Recent Bookings -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-semibold text-lg">Booking Terbaru</h2>
                        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
                    </div>

                    @if($recentBookings->isEmpty())
                        <p class="text-gray-500 text-center py-4">Belum ada booking terbaru.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($recentBookings as $booking)
                                                <div class="flex items-center p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                                                    <div class="p-2 mr-3 bg-gray-100 rounded-lg">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="font-medium text-gray-800">{{ $booking->user->name }}</p>
                                                        <div class="flex items-center text-sm text-gray-500">
                                                            <span class="mr-2">{{ $booking->table->name }}</span>
                                                            <span class="text-xs px-2 py-0.5 rounded-full {{ 
                                                                $booking->status === 'paid' ? 'bg-green-100 text-green-800' :
                                ($booking->status === 'pending' ? 'bg-amber-100 text-amber-800' :
                                    'bg-gray-100 text-gray-800') 
                                                            }}">
                                                                {{ ucfirst($booking->status) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="text-right text-sm text-gray-500">
                                                        <p>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</p>
                                                        <p>{{ \Carbon\Carbon::parse($booking->start_time)->format('d/m') }}</p>
                                                    </div>
                                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Booking Chart
                var bookingData = @json($lastWeekBookings);

                var options = {
                    series: [{
                        name: 'Booking',
                        data: bookingData.map(item => item.count)
                    }],
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '60%',
                        }
                    },
                    colors: ['#3b82f6'],
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: bookingData.map(item => item.date),
                        axisBorder: {
                            show: false
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah Booking'
                        },
                        labels: {
                            formatter: function (val) {
                                return Math.floor(val);
                            }
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " booking";
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f3f4f6',
                        strokeDashArray: 5
                    }
                };

                var chart = new ApexCharts(document.querySelector("#bookingsChart"), options);
                chart.render();
            });
        </script>
    @endpush
@endsection