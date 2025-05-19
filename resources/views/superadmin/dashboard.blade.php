@extends('layouts.super-admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
            Dashboard Super Admin
        </h1>
        <p class="text-gray-600 text-lg">Analytics & Overview dari seluruh venue</p>
    </div>

    <!-- Main Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Admin</p>
                    <p class="text-3xl font-bold">{{ $adminCount ?? 0 }}</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-users-cog text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm font-medium">Total Venue</p>
                    <p class="text-3xl font-bold">{{ $venueCount ?? 0 }}</p>
                </div>
                <div class="bg-emerald-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-building text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-medium">Total User</p>
                    <p class="text-3xl font-bold">{{ $userCount ?? 0 }}</p>
                </div>
                <div class="bg-amber-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Meja</p>
                    <p class="text-3xl font-bold">{{ $tableCount ?? 0 }}</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-table text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Revenue Comparison Chart -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-chart-bar mr-3"></i>
                    Revenue Bulan Ini
                </h3>
                <p class="text-indigo-100 text-sm">Per venue - {{ Carbon\Carbon::now()->format('F Y') }}</p>
            </div>
            <div class="p-6">
                <canvas id="revenueChart" width="400" height="300"></canvas>
            </div>
        </div>

        <!-- Popular Venues Ranking -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-pink-500 to-rose-600 px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-trophy mr-3"></i>
                    Ranking Popularitas
                </h3>
                <p class="text-pink-100 text-sm">Venue terpopuler bulan ini</p>
            </div>
            <div class="p-6">
                <!-- Filter Toggle -->
                <div class="mb-4">
                    <div class="bg-gray-100 rounded-lg p-1 flex">
                        <button 
                            id="rankingByBookings" 
                            class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200 bg-rose-500 text-white"
                            onclick="toggleRanking('bookings')">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Total Booking
                        </button>
                        <button 
                            id="rankingByRevenue" 
                            class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200 text-gray-600 hover:text-gray-800"
                            onclick="toggleRanking('revenue')">
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            Total Revenue
                        </button>
                    </div>
                </div>
                <canvas id="popularVenuesChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Revenue Chart Data
        const revenueData = @json($revenueData);
        
        // Popular Venues Data
        const popularVenuesData = @json($popularVenuesData);

        // Color Palettes
        const revenueColors = [
            'rgba(99, 102, 241, 0.8)',  // Indigo
            'rgba(168, 85, 247, 0.8)',  // Purple  
            'rgba(236, 72, 153, 0.8)',  // Pink
            'rgba(34, 197, 94, 0.8)',   // Emerald
            'rgba(251, 146, 60, 0.8)',  // Orange
        ];

        const rankingColors = [
            'rgba(239, 68, 68, 0.8)',   // Red
            'rgba(245, 158, 11, 0.8)',  // Amber
            'rgba(34, 197, 94, 0.8)',   // Emerald
            'rgba(59, 130, 246, 0.8)',  // Blue
            'rgba(168, 85, 247, 0.8)',  // Purple
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
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.x);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
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
                    borderRadius: 8,
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
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
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
                            ticks: {
                                callback: function(value) {
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
                bookingBtn.className = 'flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200 bg-rose-500 text-white';
                revenueBtn.className = 'flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200 text-gray-600 hover:text-gray-800';
                createPopularChart('bookings');
            } else {
                revenueBtn.className = 'flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200 bg-rose-500 text-white';
                bookingBtn.className = 'flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200 text-gray-600 hover:text-gray-800';
                createPopularChart('revenue');
            }
        }
    </script>
@endsection