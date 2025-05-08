<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\Table;
use App\Models\Booking;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $venue = Venue::find(auth()->user()->venue_id);

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
            ->where('status', 'paid')
            ->sum('total_amount');

        // Menghitung pendapatan bulan ini
        $monthlyRevenue = Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereHas('table', function ($query) use ($venue) {
                $query->where('venue_id', $venue->id);
            })
            ->where('status', 'paid')
            ->sum('total_amount');

        // Menghitung jumlah booking berdasarkan status
        $pendingBookings = Booking::whereHas('table', function ($query) use ($venue) {
                $query->where('venue_id', $venue->id);
            })
            ->where('status', 'pending')
            ->count();
            
        $paidBookings = Booking::whereHas('table', function ($query) use ($venue) {
                $query->where('venue_id', $venue->id);
            })
            ->where('status', 'paid')
            ->count();

        // Ambil booking terbaru
        $recentBookings = Booking::whereHas('table', function ($query) use ($venue) {
            $query->where('venue_id', $venue->id);
        })
            ->latest()
            ->take(5)
            ->with(['user', 'table'])
            ->get();

        // Menghitung data analitik untuk diagram
        $lastWeekBookings = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Booking::whereDate('created_at', $date)
                ->whereHas('table', function ($query) use ($venue) {
                    $query->where('venue_id', $venue->id);
                })
                ->count();
            $lastWeekBookings[] = [
                'date' => $date->format('d/m'),
                'count' => $count
            ];
        }

        return view('admin.dashboard', compact(
            'venue',
            'todayBookings',
            'totalTables',
            'usedTables',
            'availableTables',
            'recentBookings',
            'todayRevenue',
            'monthlyRevenue',
            'pendingBookings',
            'paidBookings',
            'lastWeekBookings'
        ));
    }
}