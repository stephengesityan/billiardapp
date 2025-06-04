@extends('layouts.super-admin')

@section('content')
    <div class="bg-gray-50 min-h-screen">
        <div class="p-6">
            <!-- Header and Welcome -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard Super Admin</h1>
                    <p class="text-gray-600 mt-1">Analytics & Overview dari seluruh venue</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ now()->format('l, d F Y') }}</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ now()->format('H:i') }}</p>
                </div>
            </div>

            <!-- Main Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Admin Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Admin</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $adminCount ?? 0 }}</p>
                        </div>
                        <div class="text-blue-500 p-2 bg-blue-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Administrator aktif</p>
                </div>

                <!-- Total Venue Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Venue</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $venueCount ?? 0 }}</p>
                        </div>
                        <div class="text-green-500 p-2 bg-green-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Venue terdaftar</p>
                </div>

                <!-- Total User Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-amber-500 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total User</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $userCount ?? 0 }}</p>
                        </div>
                        <div class="text-amber-500 p-2 bg-amber-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Pengguna terdaftar</p>
                </div>

                <!-- Total Meja Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Meja</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $tableCount ?? 0 }}</p>
                        </div>
                        <div class="text-purple-500 p-2 bg-purple-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Meja tersedia</p>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Revenue Comparison Chart -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Revenue Bulan Ini
                        </h3>
                        <p class="text-gray-500 text-sm">Per venue - {{ Carbon\Carbon::now()->format('F Y') }}</p>
                    </div>
                    <div class="p-6">
                        <div style="height: 300px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Popular Venues Ranking -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            Ranking Popularitas
                        </h3>
                        <p class="text-gray-500 text-sm">Venue terpopuler bulan ini</p>
                    </div>
                    <div class="p-6">
                        <!-- Filter Toggle -->
                        <div class="mb-4">
                            <div class="bg-gray-100 rounded-lg p-1 flex">
                                <button id="rankingByBookings"
                                    class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200 bg-green-600 text-white"
                                    onclick="toggleRanking('bookings')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Total Booking
                                </button>
                                <button id="rankingByRevenue"
                                    class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200 text-gray-600 hover:text-gray-800"
                                    onclick="toggleRanking('revenue')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Total Revenue
                                </button>
                            </div>
                        </div>
                        <div style="height: 300px;">
                            <canvas id="popularVenuesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
        <script>
            // Revenue Chart Data
            const revenueData = @json($revenueData);

            // Popular Venues Data
            const popularVenuesData = @json($popularVenuesData);

            // Color Palettes - More subtle and professional
            const revenueColors = [
                'rgba(59, 130, 246, 0.8)',   // Blue
                'rgba(16, 185, 129, 0.8)',   // Emerald
                'rgba(245, 158, 11, 0.8)',   // Amber
                'rgba(139, 92, 246, 0.8)',   // Violet
                'rgba(236, 72, 153, 0.8)',   // Pink
            ];

            const rankingColors = [
                'rgba(34, 197, 94, 0.8)',    // Green
                'rgba(59, 130, 246, 0.8)',   // Blue
                'rgba(245, 158, 11, 0.8)',   // Amber
                'rgba(139, 92, 246, 0.8)',   // Violet
                'rgba(107, 114, 128, 0.8)',  // Gray
            ];

            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: revenueData.map(item => item.venue_name),
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: revenueData.map(item => parseFloat(item.total_revenue)),
                        backgroundColor: revenueColors,
                        borderRadius: 4,
                        borderSkipped: false,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(156, 163, 175, 0.2)',
                            borderWidth: 1,
                            callbacks: {
                                label: function (context) {
                                    return 'Revenue: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.x);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            },
                            ticks: {
                                color: '#6B7280',
                                callback: function (value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                        notation: 'compact',
                                        maximumFractionDigits: 1
                                    }).format(value);
                                }
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6B7280'
                            }
                        }
                    }
                }
            });

            // Popular Venues Chart
            const popularCtx = document.getElementById('popularVenuesChart').getContext('2d');
            let popularChart;

            function createPopularChart(sortBy) {
                const sortedData = [...popularVenuesData].sort((a, b) => {
                    if (sortBy === 'revenue') {
                        return parseFloat(b.total_revenue) - parseFloat(a.total_revenue);
                    } else {
                        return parseInt(b.total_bookings) - parseInt(a.total_bookings);
                    }
                });

                const chartData = {
                    labels: sortedData.map(item => item.venue_name),
                    datasets: [{
                        label: sortBy === 'revenue' ? 'Revenue (Rp)' : 'Total Booking',
                        data: sortedData.map(item => sortBy === 'revenue' ? parseFloat(item.total_revenue) : parseInt(item.total_bookings)),
                        backgroundColor: rankingColors,
                        borderRadius: 4,
                        borderSkipped: false,
                    }]
                };

                if (popularChart) {
                    popularChart.destroy();
                }

                popularChart = new Chart(popularCtx, {
                    type: 'bar',
                    data: chartData,
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: 'rgba(156, 163, 175, 0.2)',
                                borderWidth: 1,
                                callbacks: {
                                    label: function (context) {
                                        if (sortBy === 'revenue') {
                                            return 'Revenue: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.x);
                                        } else {
                                            return 'Total Booking: ' + context.parsed.x;
                                        }
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                },
                                ticks: {
                                    color: '#6B7280',
                                    callback: function (value) {
                                        if (sortBy === 'revenue') {
                                            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                                notation: 'compact',
                                                maximumFractionDigits: 1
                                            }).format(value);
                                        } else {
                                            return value;
                                        }
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#6B7280'
                                }
                            }
                        }
                    }
                });
            }

            // Initialize popular venues chart
            createPopularChart('bookings');

            // Toggle function for ranking chart
            function toggleRanking(type) {
                const bookingBtn = document.getElementById('rankingByBookings');
                const revenueBtn = document.getElementById('rankingByRevenue');

                if (type === 'bookings') {
                    bookingBtn.className = 'flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200 bg-green-600 text-white';
                    revenueBtn.className = 'flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200 text-gray-600 hover:text-gray-800';
                    createPopularChart('bookings');
                } else {
                    revenueBtn.className = 'flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200 bg-green-600 text-white';
                    bookingBtn.className = 'flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200 text-gray-600 hover:text-gray-800';
                    createPopularChart('revenue');
                }
            }
        </script>
    @endpush
@endsection