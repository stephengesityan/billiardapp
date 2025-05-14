<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\Table;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        // Get current admin's venue ID
        $user = Auth::user();
        $adminVenueId = $user->venue_id;
        $isSuperAdmin = $user->hasRole('superadmin') || $adminVenueId === null;

        // For super admin, get the first venue or allow selection
        if ($isSuperAdmin) {
            $venue = request()->has('venue_id') 
                ? Venue::find(request('venue_id')) 
                : Venue::first();
            
            // Get all venues for dropdown selection
            $venues = Venue::all();
        } else {
            $venue = Venue::find($adminVenueId);
            $venues = collect([$venue]);
        }

        // Jika tidak ada venue, tampilkan halaman khusus
        if (!$venue) {
            return view('admin.no_venue_dashboard');
        }

        // Menghitung booking hari ini
        $todayBookings = Booking::whereDate('created_at', now())
            ->whereHas('table', function ($query) use ($venue) {
                $query->where('venue_id', $venue->id);
            })
            ->count();

        // Kalkulasi status meja
        $totalTables = Table::where('venue_id', $venue->id)->count();
        $usedTables = Table::where('venue_id', $venue->id)->where('status', 'booked')->count();
        $availableTables = Table::where('venue_id', $venue->id)->where('status', 'available')->count();

        // Menghitung pendapatan hari ini
        $todayRevenue = Booking::whereDate('created_at', now())
            ->whereHas('table', function ($query) use ($venue) {
                $query->where('venue_id', $venue->id);
            })
            ->where('bookings.status', 'paid')
            ->sum('total_amount');

        // Menghitung pendapatan bulan ini
        $monthlyRevenue = Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereHas('table', function ($query) use ($venue) {
                $query->where('venue_id', $venue->id);
            })
            ->where('bookings.status', 'paid')
            ->sum('total_amount');

        // Menghitung jumlah booking berdasarkan status
        $pendingBookings = Booking::whereHas('table', function ($query) use ($venue) {
                $query->where('venue_id', $venue->id);
            })
            ->where('bookings.status', 'pending')
            ->count();
            
        $paidBookings = Booking::whereHas('table', function ($query) use ($venue) {
                $query->where('venue_id', $venue->id);
            })
            ->where('bookings.status', 'paid')
            ->count();

        // Ambil booking terbaru
        $recentBookings = Booking::whereHas('table', function ($query) use ($venue) {
            $query->where('venue_id', $venue->id);
        })
            ->latest()
            ->take(5)
            ->with(['user', 'table'])
            ->get();

        // Menghitung data analitik untuk diagram pendapatan 7 hari terakhir
        $lastWeekRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStart = $date->copy()->startOfDay();
            $dateEnd = $date->copy()->endOfDay();
            
            $dayRevenue = Booking::whereBetween('created_at', [$dateStart, $dateEnd])
                ->whereHas('table', function ($query) use ($venue) {
                    $query->where('venue_id', $venue->id);
                })
                ->where('status', 'paid')
                ->sum('total_amount'); // Asumsikan terdapat kolom 'amount' yang menyimpan nilai pembayaran
                
            $lastWeekRevenue[] = [
                'date' => $date->format('d/m'),
                'revenue' => (float)$dayRevenue // Pastikan revenue dikonversi ke float
            ];
        }

        // NEW: MONTHLY REVENUE FOR LAST 6 MONTHS
        $lastSixMonthsRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Booking::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->whereHas('table', function ($query) use ($venue) {
                    $query->where('venue_id', $venue->id);
                })
                ->where('bookings.status', 'paid')
                ->sum('total_amount');
            
            $lastSixMonthsRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // FIXED: REVENUE PER TABLE - AMBIL DATA LEBIH FLEKSIBEL
        // Ubah untuk mengambil data dari 6 bulan terakhir jika bulan ini tidak ada data
        $tableRevenue = Booking::where(function($query) {
                // Coba ambil dari bulan ini dulu
                $query->whereMonth('bookings.created_at', now()->month)
                    ->whereYear('bookings.created_at', now()->year);
            })
            ->whereHas('table', function ($query) use ($venue) {
                $query->where('venue_id', $venue->id);
            })
            ->where('bookings.status', 'paid')
            ->select(
                'table_id',
                DB::raw('tables.name as table_name'),
                DB::raw('COUNT(*) as booking_count'),
                DB::raw('SUM(bookings.total_amount) as table_revenue')
            )
            ->join('tables', 'bookings.table_id', '=', 'tables.id')
            ->groupBy('table_id', 'tables.name')
            ->orderBy('table_revenue', 'desc')
            ->take(10)
            ->get();

        // Jika tidak ada data bulan ini, coba ambil dari 6 bulan terakhir
        if ($tableRevenue->isEmpty()) {
            $tableRevenue = Booking::where(function($query) {
                    // Ambil dari 6 bulan terakhir
                    $query->where('bookings.created_at', '>=', now()->subMonths(6));
                })
                ->whereHas('table', function ($query) use ($venue) {
                    $query->where('venue_id', $venue->id);
                })
                ->where('bookings.status', 'paid')
                ->select(
                    'table_id',
                    DB::raw('tables.name as table_name'),
                    DB::raw('COUNT(*) as booking_count'),
                    DB::raw('SUM(bookings.total_amount) as table_revenue')
                )
                ->join('tables', 'bookings.table_id', '=', 'tables.id')
                ->groupBy('table_id', 'tables.name')
                ->orderBy('table_revenue', 'desc')
                ->take(10)
                ->get();
        }

        // Jika masih tidak ada data, buat dummy data supaya chart tetap muncul
        if ($tableRevenue->isEmpty()) {
            // Ambil 5 meja dari venue ini
            $tables = Table::where('venue_id', $venue->id)
                ->take(5)
                ->get();
                
            foreach ($tables as $table) {
                $tableRevenue->push([
                    'table_id' => $table->id,
                    'table_name' => $table->name,
                    'booking_count' => 0,
                    'table_revenue' => 0
                ]);
            }
            
            // Jika tidak ada meja sama sekali, buat data dummy
            if ($tableRevenue->isEmpty()) {
                $tableRevenue = collect([
                    [
                        'table_id' => 1,
                        'table_name' => 'Meja 1',
                        'booking_count' => 0,
                        'table_revenue' => 0
                    ],
                    [
                        'table_id' => 2,
                        'table_name' => 'Meja 2',
                        'booking_count' => 0,
                        'table_revenue' => 0
                    ],
                    [
                        'table_id' => 3,
                        'table_name' => 'Meja 3',
                        'booking_count' => 0,
                        'table_revenue' => 0
                    ]
                ]);
            }
        }

        // NEW: TOP 5 USERS LEADERBOARD
        // Ambil 5 pengguna dengan jumlah booking terbanyak dari 6 bulan terakhir
        // Kecualikan users yang merupakan admin dari venue yang sedang dilihat
        $topUsers = Booking::where('bookings.created_at', '>=', now()->subMonths(6))
            ->whereHas('table', function ($query) use ($venue) {
                $query->where('venue_id', $venue->id);
            })
            ->join('users', 'bookings.user_id', '=', 'users.id')
            // Exclude users who are admins of this venue (venue_id matches current venue)
            ->where(function($query) use ($venue) {
                $query->whereNull('users.venue_id')
                      ->orWhere('users.venue_id', '!=', $venue->id);
            })
            ->select(
                'user_id',
                DB::raw('users.name as user_name'),
                DB::raw('COUNT(*) as booking_count'),
                DB::raw('SUM(bookings.total_amount) as total_spent')
            )
            ->groupBy('user_id', 'users.name')
            ->orderBy('booking_count', 'desc')
            ->take(5)
            ->get();
            
        // Jika tidak ada data, buat array kosong yang terstruktur
        if ($topUsers->isEmpty()) {
            $topUsers = collect([
                [
                    'user_id' => 1,
                    'user_name' => 'Belum ada data',
                    'booking_count' => 0,
                    'total_spent' => 0
                ]
            ]);
        }

        return view('admin.dashboard', compact(
            'venue',
            'venues',
            'isSuperAdmin',
            'todayBookings',
            'totalTables',
            'usedTables',
            'availableTables',
            'recentBookings',
            'todayRevenue',
            'monthlyRevenue',
            'pendingBookings',
            'paidBookings',
            'lastWeekRevenue',
            'lastSixMonthsRevenue',
            'tableRevenue',
            'topUsers'
            // Hapus 'revenueByDay' dari compact
        ));
    }
}