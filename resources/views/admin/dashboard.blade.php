@extends('layouts.admin')

@section('content')
    <div class="bg-gray-50 min-h-screen">
        <div class="p-6">
            <!-- Header and Welcome -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 space-y-4 sm:space-y-0">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Dashboard {{ $venue->name }}</h1>
                    <p class="text-gray-600 mt-1">Selamat datang, {{ auth()->user()->name }}!</p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</p>
                    <p class="text-xl sm:text-2xl font-semibold text-gray-800">{{ now()->translatedFormat('H:i') }}</p>
                </div>
            </div>


            <!-- Stats Cards - Row 1: Revenue and Booking Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Today's Revenue -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Pendapatan Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-800">Rp{{ number_format($todayRevenue, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="text-green-500 p-2 bg-green-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Pendapatan Bulan Ini:
                        Rp{{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
                </div>

                <!-- Today's Bookings -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Booking Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $todayBookings }}</p>
                        </div>
                        <div class="text-blue-500 p-2 bg-blue-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex mt-2 space-x-4">
                        {{-- <p class="text-xs text-gray-500">Pending: <span class="font-semibold text-amber-500">{{
                                $pendingBookings }}</span></p> --}}
                        <p class="text-xs text-gray-500">Paid: <span
                                class="font-semibold text-green-500">{{ $paidBookings }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Row 2: Top 5 Pelanggan Loyal Leaderboard - Full Width -->
            <div class="mb-6">
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-md transition">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">🏆 Top 5 Pelanggan Loyal</h2>
                        <div class="text-purple-500 p-2 bg-purple-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    @if(!empty($topUsers) && count($topUsers) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            @foreach($topUsers->take(5) as $index => $user)
                                <div
                                    class="flex flex-col items-center p-4 {{ $index === 0 ? 'bg-gradient-to-br from-yellow-50 to-yellow-100 border-2 border-yellow-300' : ($index === 1 ? 'bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-gray-300' : 'bg-gradient-to-br from-orange-50 to-orange-100 border-2 border-orange-300') }} rounded-lg text-center">
                                    <div class="mb-3">
                                        @if($index === 0)
                                            <span
                                                class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 text-white rounded-full text-2xl font-bold shadow-lg">🥇</span>
                                        @elseif($index === 1)
                                            <span
                                                class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-gray-300 to-gray-500 text-white rounded-full text-2xl font-bold shadow-lg">🥈</span>
                                        @else
                                            <span
                                                class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-full text-2xl font-bold shadow-lg">🥉</span>
                                        @endif
                                    </div>
                                    <div class="mb-2">
                                        <h3 class="font-bold text-sm text-gray-800 mb-1">{{ Str::limit($user['user_name'], 12) }}
                                        </h3>
                                        <div class="text-xs text-gray-600">
                                            Ranking #{{ $index + 1 }}
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-full px-3 py-1 shadow-sm">
                                        <p class="text-lg font-bold text-gray-800">{{ $user['booking_count'] }}</p>
                                        <p class="text-xs text-gray-500">Booking</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <p class="text-gray-500 text-sm">Belum ada data pelanggan loyal</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Main Performance Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Monthly Revenue Chart -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-semibold text-lg">Tren Pendapatan Bulanan</h2>
                    </div>
                    <div class="h-80" id="monthlyRevenueChart"></div>
                </div>

                <!-- Recent Bookings -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-semibold text-lg">Booking Terbaru</h2>
                        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-blue-600 hover:underline">Lihat
                            Semua</a>
                    </div>

                    @if($recentBookings->isEmpty())
                        <p class="text-gray-500 text-center py-4">Belum ada booking terbaru.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($recentBookings as $booking)
                                        <div class="flex items-center p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                                            <div class="p-2 mr-3 bg-gray-100 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-800">{{ $booking->user->name }}</p>
                                                <div class="flex items-center text-sm text-gray-500">
                                                    <span class="mr-2">{{ $booking->table->name }}</span>
                                                    <span
                                                        class="text-xs px-2 py-0.5 rounded-full {{ 
                                                                                                                                                                                                                                                            $booking->status === 'paid' ? 'bg-green-100 text-green-800' :
                                ($booking->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800') 
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

            <!-- Secondary Performance Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Weekly Revenue Chart -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="font-semibold text-lg mb-4">Pendapatan 7 Hari Terakhir</h2>
                    <div class="h-80" id="weeklyRevenueChart"></div>
                </div>

                <!-- Table Revenue Performance -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-semibold text-lg">Performa Pendapatan per Meja (Bulan Ini)</h2>
                    </div>
                    <div class="h-80" id="tableRevenueChart"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Monthly Revenue Chart
                var monthlyRevenueData = @json($lastSixMonthsRevenue);

                var monthlyRevenueOptions = {
                    series: [{
                        name: 'Pendapatan',
                        data: monthlyRevenueData.map(item => item.revenue)
                    }],
                    chart: {
                        type: 'area',
                        height: 300,
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    colors: ['#10b981'],
                    xaxis: {
                        categories: monthlyRevenueData.map(item => item.month),
                        axisBorder: {
                            show: false
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Pendapatan (Rp)'
                        },
                        labels: {
                            formatter: function (val) {
                                return 'Rp' + val.toLocaleString('id-ID');
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return 'Rp' + val.toLocaleString('id-ID');
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: "vertical",
                            shadeIntensity: 0.4,
                            inverseColors: false,
                            opacityFrom: 0.8,
                            opacityTo: 0.2,
                            stops: [0, 100]
                        }
                    },
                    grid: {
                        borderColor: '#f3f4f6',
                        strokeDashArray: 5
                    },
                    markers: {
                        size: 5,
                        colors: ['#10b981'],
                        strokeColor: '#fff',
                        strokeWidth: 2
                    },
                    title: {
                        text: 'Tren Pendapatan Bulanan',
                        align: 'center',
                        style: {
                            fontSize: '18px',
                            fontWeight: 'medium'
                        }
                    }
                };

                var monthlyRevenueChart = new ApexCharts(document.querySelector("#monthlyRevenueChart"), monthlyRevenueOptions);
                monthlyRevenueChart.render();

                // Weekly Revenue Chart
                var weeklyRevenueData = @json($lastWeekRevenue);

                var options = {
                    series: [{
                        name: 'Pendapatan',
                        data: weeklyRevenueData.map(item => item.revenue)
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
                    colors: ['#10b981'],
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: weeklyRevenueData.map(item => item.date),
                        axisBorder: {
                            show: false
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Pendapatan (Rp)'
                        },
                        labels: {
                            formatter: function (val) {
                                return 'Rp' + Math.floor(val).toLocaleString('id-ID');
                            }
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return 'Rp' + val.toLocaleString('id-ID');
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f3f4f6',
                        strokeDashArray: 5
                    }
                };

                var chart = new ApexCharts(document.querySelector("#weeklyRevenueChart"), options);
                chart.render();

                // Table Revenue Performance Chart
                var tableRevenueData = @json($tableRevenue);

                // Verifikasi data tersedia dan lengkap
                if (!tableRevenueData || tableRevenueData.length === 0) {
                    document.getElementById("tableRevenueChart").innerHTML =
                        '<div class="flex items-center justify-center h-full"><p class="text-gray-500">Tidak ada data tersedia</p></div>';
                } else {
                    var tableRevenueOptions = {
                        series: [{
                            name: 'Pendapatan',
                            data: tableRevenueData.map(item => item.table_revenue || 0)
                        }, {
                            name: 'Jumlah Booking',
                            data: tableRevenueData.map(item => item.booking_count || 0)
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
                                horizontal: false,
                                columnWidth: '55%',
                                endingShape: 'rounded'
                            }
                        },
                        colors: ['#f97316', '#3b82f6'],
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        xaxis: {
                            categories: tableRevenueData.map(item => item.table_name || 'Tidak ada nama')
                        },
                        yaxis: [
                            {
                                title: {
                                    text: 'Pendapatan (Rp)'
                                },
                                labels: {
                                    formatter: function (val) {
                                        return 'Rp' + Math.floor(val).toLocaleString('id-ID');
                                    }
                                }
                            },
                            {
                                opposite: true,
                                title: {
                                    text: 'Jumlah Booking'
                                },
                                labels: {
                                    formatter: function (val) {
                                        return Math.floor(val);
                                    }
                                }
                            }
                        ],
                        tooltip: {
                            y: {
                                formatter: function (val, { seriesIndex }) {
                                    if (seriesIndex === 0) {
                                        return 'Rp' + val.toLocaleString('id-ID');
                                    } else {
                                        return val + ' booking';
                                    }
                                }
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'left'
                        },
                        title: {
                            text: 'Analisis Performa Meja',
                            align: 'center',
                            style: {
                                fontSize: '18px',
                                fontWeight: 'medium'
                            }
                        }
                    };

                    var tableRevenueChart = new ApexCharts(document.querySelector("#tableRevenueChart"), tableRevenueOptions);
                    tableRevenueChart.render();
                }
            });
        </script>
    @endpush
@endsection