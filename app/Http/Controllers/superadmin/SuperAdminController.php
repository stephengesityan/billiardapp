<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\Venue;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    /**
     * Display the superadmin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Basic counts
        $adminCount = User::where('role', 'admin')->count();
        $venueCount = Venue::count();
        $userCount = User::where('role', 'user')->count();
        $tableCount = Table::count();

        // Revenue comparison for current month
        $currentMonth = Carbon::now()->startOfMonth();
        $revenueData = $this->getMonthlyRevenueByVenue($currentMonth);

        // Popular venues ranking data
        $popularVenuesData = $this->getPopularVenuesData($currentMonth);

        return view('superadmin.dashboard', compact(
            'adminCount',
            'venueCount',
            'userCount',
            'tableCount',
            'revenueData',
            'popularVenuesData'
        ));
    }

    /**
     * Get monthly revenue by venue for chart
     */
    private function getMonthlyRevenueByVenue($startDate)
    {
        return Venue::leftJoin('tables', 'venues.id', '=', 'tables.venue_id')
            ->leftJoin('bookings', function($join) use ($startDate) {
                $join->on('tables.id', '=', 'bookings.table_id')
                     ->where('bookings.status', 'paid')
                     ->where('bookings.created_at', '>=', $startDate)
                     ->where('bookings.created_at', '<', $startDate->copy()->endOfMonth());
            })
            ->select('venues.name as venue_name', 
                    DB::raw('COALESCE(SUM(bookings.total_amount), 0) as total_revenue'))
            ->groupBy('venues.id', 'venues.name')
            ->get();
    }

    /**
     * Get popular venues data for ranking
     */
    private function getPopularVenuesData($startDate)
    {
        return Venue::leftJoin('tables', 'venues.id', '=', 'tables.venue_id')
            ->leftJoin('bookings', function($join) use ($startDate) {
                $join->on('tables.id', '=', 'bookings.table_id')
                     ->where('bookings.status', 'paid')
                     ->where('bookings.created_at', '>=', $startDate)
                     ->where('bookings.created_at', '<', $startDate->copy()->endOfMonth());
            })
            ->select('venues.name as venue_name',
                    DB::raw('COALESCE(COUNT(bookings.id), 0) as total_bookings'),
                    DB::raw('COALESCE(SUM(bookings.total_amount), 0) as total_revenue'))
            ->groupBy('venues.id', 'venues.name')
            ->get();
    }
}