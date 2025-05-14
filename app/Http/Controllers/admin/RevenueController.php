<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Table;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        // Default filter periode (bulan ini)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Get current admin's venue ID
        $user = Auth::user();
        $adminVenueId = $user->venue_id; // Asumsi: Admin memiliki venue_id yang menunjukkan venue yang mereka kelola
        
        // Jika admin adalah super admin (bisa melihat semua venue)
        $isSuperAdmin = $user->hasRole('superadmin') || $adminVenueId === null; // Asumsi: Super admin tidak memiliki venue_id spesifik atau memiliki role khusus
        
        // Query untuk mengambil data venue untuk filter (hanya venue yang dikelola oleh admin atau semua venue untuk super admin)
        if ($isSuperAdmin) {
            $venues = Venue::all();
            $venueId = $request->input('venue_id');
        } else {
            $venues = Venue::where('id', $adminVenueId)->get();
            $venueId = $adminVenueId; // Force venue id ke venue yang dikelola admin
        }
        
        // Base query untuk pendapatan
        $revenueQuery = Booking::with('table.venue')
            ->where('bookings.status', 'paid')
            ->whereBetween(DB::raw('DATE(bookings.start_time)'), [$startDate, $endDate]);
            
        // Filter berdasarkan venue yang dikelola admin atau yang dipilih oleh super admin
        if (!$isSuperAdmin) {
            // Admin venue biasa hanya bisa melihat venuenya sendiri
            $revenueQuery->whereHas('table', function($query) use ($adminVenueId) {
                $query->where('venue_id', $adminVenueId);
            });
        } elseif ($venueId) {
            // Super admin bisa memilih venue tertentu
            $revenueQuery->whereHas('table', function($query) use ($venueId) {
                $query->where('venue_id', $venueId);
            });
        }
        
        // Get summary total pendapatan
        $totalRevenue = $revenueQuery->sum('total_amount');
        
        // Get total bookings
        $totalBookings = $revenueQuery->count();
        
        // Get revenue per venue - dengan filter sesuai akses admin
        $revenuePerVenueQuery = Booking::with('table.venue')
            ->where('bookings.status', 'paid')
            ->whereBetween(DB::raw('DATE(bookings.start_time)'), [$startDate, $endDate]);
            
        if (!$isSuperAdmin) {
            $revenuePerVenueQuery->whereHas('table', function($query) use ($adminVenueId) {
                $query->where('venue_id', $adminVenueId);
            });
        }
        
        $revenuePerVenue = $revenuePerVenueQuery
            ->select(
                'tables.venue_id',
                DB::raw('venues.name as venue_name'),
                DB::raw('COUNT(*) as total_bookings'),
                DB::raw('SUM(bookings.total_amount) as total_revenue')
            )
            ->join('tables', 'bookings.table_id', '=', 'tables.id')
            ->join('venues', 'tables.venue_id', '=', 'venues.id')
            ->groupBy('tables.venue_id', 'venues.name')
            ->get();
            
        // Get revenue per table (Untuk admin biasa, selalu tampilkan detail meja venuenya)
        // Untuk super admin, detail meja hanya muncul jika venue tertentu dipilih
        $revenuePerTable = null;
        if (!$isSuperAdmin || $venueId) {
            $venueIdForTable = $isSuperAdmin ? $venueId : $adminVenueId;
            
            $revenuePerTable = Booking::with('table')
                ->where('bookings.status', 'paid')
                ->whereBetween(DB::raw('DATE(bookings.start_time)'), [$startDate, $endDate])
                ->whereHas('table', function($query) use ($venueIdForTable) {
                    $query->where('venue_id', $venueIdForTable);
                })
                ->select(
                    'table_id',
                    DB::raw('tables.name as table_name'),
                    DB::raw('COUNT(*) as booking_count'),
                    DB::raw('SUM(bookings.total_amount) as table_revenue')
                )
                ->join('tables', 'bookings.table_id', '=', 'tables.id')
                ->groupBy('table_id', 'tables.name')
                ->get();
        }
        
        // Get data untuk chart pendapatan harian dalam periode
        $dailyRevenueQuery = Booking::with('table.venue')
            ->where('bookings.status', 'paid')
            ->whereBetween(DB::raw('DATE(bookings.start_time)'), [$startDate, $endDate]);
            
        if (!$isSuperAdmin) {
            $dailyRevenueQuery->whereHas('table', function($query) use ($adminVenueId) {
                $query->where('venue_id', $adminVenueId);
            });
        } elseif ($venueId) {
            $dailyRevenueQuery->whereHas('table', function($query) use ($venueId) {
                $query->where('venue_id', $venueId);
            });
        }
        
        $dailyRevenue = $dailyRevenueQuery
            ->select(
                DB::raw('DATE(bookings.start_time) as date'),
                DB::raw('SUM(bookings.total_amount) as revenue')
            )
            ->groupBy(DB::raw('DATE(bookings.start_time)'))
            ->orderBy('date', 'asc')
            ->get();
            
        return view('admin.revenues.index', compact(
            'venues',
            'venueId',
            'totalRevenue',
            'totalBookings',
            'revenuePerVenue',
            'revenuePerTable',
            'dailyRevenue',
            'startDate',
            'endDate',
            'isSuperAdmin'
        ));
    }
    
    public function detail(Request $request, $tableId)
    {
        // Default filter periode (bulan ini)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Get table detail
        $table = Table::with('venue')->findOrFail($tableId);
        
        // Cek apakah admin memiliki akses ke meja ini
        $user = Auth::user();
        $adminVenueId = $user->venue_id;
        $isSuperAdmin = $user->hasRole('superadmin') || $adminVenueId === null;
        
        // Jika bukan super admin dan meja bukan dari venue yang dikelola, tolak akses
        if (!$isSuperAdmin && $table->venue_id != $adminVenueId) {
            abort(403, 'Tidak memiliki akses ke meja ini');
        }
        
        // Query untuk detail booking meja tersebut
        $bookings = Booking::where('table_id', $tableId)
            ->where('bookings.status', 'paid')
            ->whereBetween(DB::raw('DATE(bookings.start_time)'), [$startDate, $endDate])
            ->with('user')
            ->orderBy('start_time', 'desc')
            ->get();
            
        // Hitung total pendapatan untuk meja ini di periode
        $totalRevenue = $bookings->sum('total_amount');
        
        // Hitung total jam penggunaan
        $totalHours = $bookings->sum(function($booking) {
            $start = Carbon::parse($booking->start_time);
            $end = Carbon::parse($booking->end_time);
            return $end->diffInHours($start);
        });
        
        return view('admin.revenues.detail', compact(
            'table',
            'bookings',
            'totalRevenue',
            'totalHours',
            'startDate',
            'endDate'
        ));
    }
    
    public function export(Request $request)
    {
        // Get current admin's venue ID
        $user = Auth::user();
        $adminVenueId = $user->venue_id;
        $isSuperAdmin = $user->hasRole('superadmin') || $adminVenueId === null;
        
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $venueId = $isSuperAdmin ? $request->input('venue_id') : $adminVenueId;
        
        // Base query untuk pendapatan
        $bookingsQuery = Booking::with(['table.venue', 'user'])
            ->where('bookings.status', 'paid')
            ->whereBetween(DB::raw('DATE(bookings.start_time)'), [$startDate, $endDate]);
            
        // Filter berdasarkan venue sesuai hak akses admin
        if (!$isSuperAdmin) {
            $bookingsQuery->whereHas('table', function($query) use ($adminVenueId) {
                $query->where('venue_id', $adminVenueId);
            });
        } elseif ($venueId) {
            $bookingsQuery->whereHas('table', function($query) use ($venueId) {
                $query->where('venue_id', $venueId);
            });
        }
        
        $bookings = $bookingsQuery->get();
        
        // Export logic using Laravel Excel or simple CSV download
        // For now we'll return a simple array that could be converted to CSV/Excel
        $exportData = [];
        
        foreach ($bookings as $booking) {
            $exportData[] = [
                'id' => $booking->id,
                'user' => $booking->user->name,
                'venue' => $booking->table->venue->name,
                'table' => $booking->table->name,
                'start_time' => $booking->start_time->format('Y-m-d H:i'),
                'end_time' => $booking->end_time->format('Y-m-d H:i'),
                'duration_hours' => $booking->end_time->diffInHours($booking->start_time),
                'payment_method' => $booking->payment_method,
                'total_amount' => $booking->total_amount,
            ];
        }
        
        // Return CSV response (simplified example)
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="venue-revenue-report.csv"',
        ];
        
        // Convert array to CSV string
        $callback = function() use ($exportData) {
            $file = fopen('php://output', 'w');
            // Header row
            fputcsv($file, array_keys($exportData[0] ?? []));
            
            // Data rows
            foreach ($exportData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}